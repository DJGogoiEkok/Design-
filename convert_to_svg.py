import re

with open('drawing.html', 'r') as f:
    content = f.read()

# Extract the SVG part
svg_match = re.search(r'(<svg[^>]*>)(.*?)(</svg>)', content, re.DOTALL)
if svg_match:
    svg_open = svg_match.group(1)
    svg_body = svg_match.group(2)
    svg_close = svg_match.group(3)
    
    # Add the style
    style = """<style>
    path { fill:none; stroke:#1a1d24; stroke-width:2.4; stroke-linecap:round; stroke-linejoin:round; stroke-dasharray:1; stroke-dashoffset:1; animation:draw var(--t) linear var(--d) forwards; }
    @keyframes draw { to { stroke-dashoffset:0; } }
  </style>"""
    
    with open('images/site/hero-bg.svg', 'w') as out:
        out.write(svg_open + '\n' + style + '\n' + svg_body + '\n' + svg_close)
    print("Successfully created images/site/hero-bg.svg")
else:
    print("Could not find SVG")
