const fs = require('fs');
const css = fs.readFileSync('css/style.css', 'utf8');
let depth = 0;
for (let i = 0; i < css.length; i++) {
    if (css[i] === '{') depth++;
    if (css[i] === '}') depth--;
}
console.log("Final depth:", depth);
