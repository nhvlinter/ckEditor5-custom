
const path = require('path');
const CKEditorWebpackPlugin = require( './node_modules/@ckeditor/ckeditor5-dev-webpack-plugin' );
// const CKFinder = require( './assets/plugin/ckfinder/ckfinder' );
const { styles } = require( './node_modules/@ckeditor/ckeditor5-dev-utils' );
module.exports = {
    // entry : [
    //     './assets/plugin/ckfinder/ckfinder.js',
    //     './assets/js/script.js'
    // ],
    // output : {
    //     filename : './public/js/bundle.js'
    // },
    entry : {
        script : './assets/js/script.js'
    },
    output : {
        filename : './public/js/[name].bundle.js',
        path: path.resolve(__dirname, 'dist')
    },
    mode: 'development',
    // plugins: [CKFinder],
    module: {
        rules: [
            {
                test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
                use: [ 'raw-loader' ]
            },
            {
                test: /ckeditor5-[^/\\]+[/\\]theme[/\\].+\.css$/,
                use: [
                    {
                        loader: 'style-loader',
                        options: {
                            injectType: 'singletonStyleTag',
                            attributes: {
                                'data-cke': true
                            }
                        }
                    },
                    {
                        loader: 'postcss-loader',
                        options: styles.getPostCssConfig( {
                            themeImporter: {
                                themePath: require.resolve( '@ckeditor/ckeditor5-theme-lark' )
                            },
                            minify: true
                        } )
                    },
                ]
            }
        ]
    }
};