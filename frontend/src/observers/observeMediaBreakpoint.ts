import {observeMedias} from "./observeMedia";
import {MediaBreakpoint} from "../config";

export function observeMediaBreakpoint(
  handler:(v:string) => void,
) {
  const xs = observeMedias(
    Object.entries(MediaBreakpoint)
    .filter(([, n]) => n > 0)
    .sort(([, m], [, n]) => -(m-n))
    .map(([breakpoint, n]) => ({
      query:`(min-width: ${n}px)`,
      value: breakpoint,
    })),
    (xs) => handler(getValues(xs))
  );
  return getValues(xs);
  function getValues(xs:string[]) {
    return xs.length > 0 ? xs[0] : "xs";
  }
}
