const fs = require('fs');
const lines = fs.readFileSync('/Users/dj/.gemini/antigravity/brain/7d467311-a8ba-4514-b8c6-931f16a9f395/.system_generated/logs/transcript_full.jsonl', 'utf8').split('\n');

for (let i = 0; i < lines.length; i++) {
    if (!lines[i]) continue;
    const step = JSON.parse(lines[i]);
    if (step.content && step.content.includes('footer-wrap')) {
        console.log("Found footer-wrap at step", step.step_index);
    }
}
