const fs = require('fs');
const lines = fs.readFileSync('/Users/dj/.gemini/antigravity/brain/7d467311-a8ba-4514-b8c6-931f16a9f395/.system_generated/logs/transcript_full.jsonl', 'utf8').split('\n');

for (let i = 0; i < lines.length; i++) {
    if (!lines[i]) continue;
    const step = JSON.parse(lines[i]);
    if (step.type === 'TOOL_RESPONSE' && step.content) {
        if (step.content.includes('Total Lines: 2530') || (step.content.includes('style.css') && step.content.includes('footer'))) {
            if (step.content.length > 5000) {
                console.log("Found view_file output at step", step.step_index);
                fs.writeFileSync('css/style_recovered_view.txt', step.content);
                console.log("Saved to css/style_recovered_view.txt");
            }
        }
    }
}
