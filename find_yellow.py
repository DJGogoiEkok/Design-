from PIL import Image

img = Image.open('/Users/dj/.gemini/antigravity/brain/7d467311-a8ba-4514-b8c6-931f16a9f395/media__1784313685163.png').convert('RGB')
w, h = img.size

# Find all yellow-ish pixels
yellow_pixels = []
for y in range(h):
    for x in range(w):
        r, g, b = img.getpixel((x, y))
        if r > 200 and g > 150 and b < 100:  # Rough yellow check
            yellow_pixels.append((x, y))

if not yellow_pixels:
    print("No yellow found.")
else:
    # Separate into top half and bottom half
    top_pixels = [p for p in yellow_pixels if p[1] < h/2]
    bottom_pixels = [p for p in yellow_pixels if p[1] >= h/2]

    def get_bbox(pixels):
        if not pixels: return None
        xs = [p[0] for p in pixels]
        ys = [p[1] for p in pixels]
        return (min(xs), min(ys), max(xs), max(ys))

    top_bbox = get_bbox(top_pixels)
    bottom_bbox = get_bbox(bottom_pixels)

    def print_bbox(name, bbox):
        if bbox:
            minx, miny, maxx, maxy = bbox
            bw, bh = maxx - minx, maxy - miny
            print(f"{name}:")
            print(f"  left: {minx/w*100:.1f}%")
            print(f"  top: {miny/h*100:.1f}%")
            print(f"  width: {bw/w*100:.1f}%")
            print(f"  height: {bh/h*100:.1f}%")

    print_bbox("Top Sign", top_bbox)
    print_bbox("Bottom Sign", bottom_bbox)
