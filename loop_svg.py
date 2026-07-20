import re

with open('images/site/hero-bg.svg', 'r') as f:
    content = f.read()

# We need to replace the style block to remove the CSS animation
content = re.sub(r'animation:draw.*?forwards;\s*\}', '}', content)
content = re.sub(r'@keyframes draw.*?\}\s*\}', '', content)

total_duration = 20.0

def replace_path(match):
    d_attr = match.group(1)
    style_attr = match.group(2)
    
    # Extract --d and --t
    d_match = re.search(r'--d:([\d.]+)s', style_attr)
    t_match = re.search(r'--t:([\d.]+)s', style_attr)
    
    if not d_match or not t_match:
        return match.group(0)
        
    delay = float(d_match.group(1))
    duration = float(t_match.group(1))
    
    # Calculate keyTimes
    start_time = delay / total_duration
    end_time = (delay + duration) / total_duration
    
    # Ensure end_time doesn't exceed 1.0 (though max is 17.4s / 20 = 0.87)
    if end_time > 1.0: end_time = 1.0
    
    # Format to 4 decimal places
    start_str = f"{start_time:.4f}"
    end_str = f"{end_time:.4f}"
    
    # Values: start at 1 (hidden), stay 1 until start_time, go to 0 at end_time, stay 0 until 1
    # Actually stroke-dashoffset defaults to 1 from CSS.
    animate_tag = f'<animate attributeName="stroke-dashoffset" values="1;1;0;0" keyTimes="0;{start_str};{end_str};1" dur="{total_duration}s" repeatCount="indefinite" />'
    
    return f'<path {d_attr} pathLength="1">{animate_tag}</path>'

# Replace all paths
# Matches: <path d="..." pathLength="1" style="--d:0.000s;--t:0.498s"/>
new_content = re.sub(r'<path (d="[^"]+") pathLength="1" style="([^"]+)"/>', replace_path, content)

with open('images/site/hero-bg.svg', 'w') as f:
    f.write(new_content)

print("SVG updated with SMIL animation for infinite looping.")
