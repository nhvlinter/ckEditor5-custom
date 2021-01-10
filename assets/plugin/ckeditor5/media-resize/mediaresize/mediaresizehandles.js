/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

/**
 * @module media/mediaresize/mediaresizehandles
 */

import Plugin from '@ckeditor/ckeditor5-core/src/plugin';
import WidgetResize from '@ckeditor/ckeditor5-widget/src/widgetresize';
import MediaLoadObserver from '../media/medialoadobserver';

/**
 * The media resize by handles feature.
 *
 * It adds the ability to resize each media using handles or manually by
 * {@link module:media/mediaresize/mediaresizebuttons~MediaResizeButtons} buttons.
 *
 * @extends module:core/plugin~Plugin
 */
export default class MediaResizeHandles extends Plugin {
	/**
	 * @inheritDoc
	 */
	static get requires() {
		return [ WidgetResize ];
	}

	/**
	 * @inheritDoc
	 */
	static get pluginName() {
		return 'MediaResizeHandles';
	}

	/**
	 * @inheritDoc
	 */
	init() {
		const command = this.editor.commands.get( 'mediaResize' );
		this.bind( 'isEnabled' ).to( command );

		this._setupResizerCreator();
	}

	/**
	 * Attaches the listeners responsible for creating a resizer for each media, except for medias inside the HTML embed preview.
	 *
	 * @private
	 */
	_setupResizerCreator() {
		const editor = this.editor;
		const editingView = editor.editing.view;

		editingView.addObserver( MediaLoadObserver );

		this.listenTo( editingView.document, 'mediaLoaded', ( evt, domEvent ) => {
			// The resizer must be attached only to medias loaded by the `MediaEmbed` plugins.
			if ( !domEvent.target.matches( 'figure.media.ck-widget > .ck-media__wrapper > div > iframe' ) ) {
				return;
			}

			const mediaView = editor.editing.view.domConverter.domToView( domEvent.target );
			const widgetView = mediaView.findAncestor( 'figure' );
			let resizer = this.editor.plugins.get( WidgetResize ).getResizerByViewElement( widgetView );
			if ( resizer ) {
				// There are rare cases when medias will be triggered multiple times for the same widget, e.g. when
				// medias's src was changed after upload (https://github.com/ckeditor/ckeditor5/pull/8108#issuecomment-708302992).
				resizer.redraw();

				return;
			}

			const mapper = editor.editing.mapper;
			const mediaModel = mapper.toModelElement( widgetView );

			resizer = editor.plugins
				.get( WidgetResize )
				.attachTo( {
					unit: editor.config.get( 'media.resizeUnit' ),

					modelElement: mediaModel,
					viewElement: widgetView,
					editor,

					getHandleHost( domWidgetElement ) {
						return domWidgetElement.querySelector( 'iframe' );
					},
					getResizeHost( domWidgetElement ) {
						return domWidgetElement;
					},
					// TODO consider other positions.
					isCentered() {
						const mediaStyle = mediaModel.getAttribute( 'mediaStyle' );

						return !mediaStyle || mediaStyle == 'full' || mediaStyle == 'alignCenter';
					},

					onCommit( newValue ) {
						editor.execute( 'mediaResize', { width: newValue } );
					}
				} );

			resizer.on( 'updateSize', () => {
				if ( !widgetView.hasClass( 'media_resized' ) ) {
					editingView.change( writer => {
						writer.addClass( 'media_resized', widgetView );
					} );
				}
			} );

			resizer.bind( 'isEnabled' ).to( this );
		} );
	}
}
