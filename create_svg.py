import base64

with open('/Users/dj/.gemini/antigravity/brain/7d467311-a8ba-4514-b8c6-931f16a9f395/important_notice_tiles_1784313108898.jpg', 'rb') as f:
    img_data = f.read()

b64 = base64.b64encode(img_data).decode('utf-8')

svg_content = f"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1024 1024" width="100%" height="100%" preserveAspectRatio="xMidYMid slice">
  <style>
    .swing {{
      transform-origin: 50% 50%;
      animation: sway 6s ease-in-out infinite alternate;
    }}
    @keyframes sway {{
      0% {{ transform: rotate(-2deg) scale(1.05); }}
      100% {{ transform: rotate(2deg) scale(1.05); }}
    }}
  </style>
  <g class="swing">
    <image href="data:image/jpeg;base64,{b64}" width="1024" height="1024" preserveAspectRatio="none" />
  </g>
</svg>"""

with open('images/site/important-animated.svg', 'w') as f:
    f.write(svg_content)
    
print("Updated SVG successfully with square viewBox.")
