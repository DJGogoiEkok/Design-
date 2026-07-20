const fs = require('fs');
const lines = fs.readFileSync('/Users/dj/.gemini/antigravity/brain/7d467311-a8ba-4514-b8c6-931f16a9f395/.system_generated/logs/transcript_full.jsonl', 'utf8').split('\n');

for (let i = 0; i < lines.length; i++) {
    if (!lines[i]) continue;
    const step = JSON.parse(lines[i]);
    if (step.type === 'RUN_COMMAND' && step.content) {
        if (step.content.includes('2530 css/style.css') || step.content.includes('/* ============================================================') || step.content.includes('.footer-wrap')) {
            // Check if this looks like the cat output
            if (step.content.length > 10000) {
                console.log("Found a massive output at step", step.step_index);
                fs.writeFileSync('css/style_recovered.css', step.content);
                console.log("Saved to css/style_recovered.css");
                process.exit(0);
            }
        }
    }
}
console.log("Not found.");
