
function Usage() {
  const state = {
    /* @observable */ isMobile: false,
    /* @action     */ setIsMobile(v:boolean){ this.isMobile = v }
  };
  observeMedia("(max-width: 599px)", state.setIsMobile);
}

export function observeMedia(query:string, handler:(matches:boolean) => void) {
  const q = window.matchMedia(query);
  q.addListener(() => { handler(q.matches) });
  return q.matches;
}

export function observeMedias<T>(
  spec:{query:string, value:T}[],
  handler:(v:T[]) => void,
) {
  const xs = spec.map(s => ({q: window.matchMedia(s.query), value:s.value}));
  xs.forEach((x) => {
    x.q.addListener(handle);
  });
  return getValues();
  function handle() {
    handler(getValues());
  }
  function getValues() {
    return xs.filter(x => x.q.matches).map(x => x.value);
  }
}
