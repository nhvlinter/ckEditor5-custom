// https://stackoverflow.com/a/11268104
const variations = [
    /* digits   */ /\d/,
    /* lower    */ /[a-z]/,
    /* upper    */ /[A-Z]/,
    /* nonWords */ /\W/,
];
export function scorePassword(pass: string):number {
    if (!pass) return 0;

    // award every unique letter until 5 repetitions
    let score = 0;
    pass.split("").reduce<{[char:string]: number}>((m, c) => (
        m[c] = (m[c] || 0) + 1,
        score += (5 / m[c]),
        m
    ), {});

    // bonus points for mixing it up
    const variationBonus = variations.map<number>(v => v.test(pass) ? 1 : 0).reduce((s, x) => s + x, 0);

    return (score + (variationBonus - 1) * 10);
}

export function checkPassStrength(pass:string) {
    const score = scorePassword(pass);
    if (score > 80) return "strong";
    if (score > 60) return "good";
    if (score >= 30) return "weak";
    return "";
}
