import cv2
import numpy as np

img = cv2.imread('/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign/images/portfolio/featured-windows.jpg', cv2.IMREAD_GRAYSCALE)
height, width = img.shape

# Threshold to get dark window frames vs bright wall
_, thresh = cv2.threshold(img, 150, 255, cv2.THRESH_BINARY_INV)

# Find contours
contours, _ = cv2.findContours(thresh, cv2.RETR_TREE, cv2.CHAIN_APPROX_SIMPLE)

rects = []
for cnt in contours:
    x, y, w, h = cv2.boundingRect(cnt)
    # Filter for window-like shapes
    if h > height * 0.4 and w > width * 0.1:
        rects.append((x, y, w, h))

# Filter to get the 4 main bounding boxes (largest area)
# Sometimes the frame and the glass inside are different contours, so we just take the 4 largest disjoint ones, or just group by x.
rects.sort(key=lambda r: r[2] * r[3], reverse=True)

# Keep the 4 largest that don't overlap too much
final_rects = []
for r in rects:
    x, y, w, h = r
    overlap = False
    for fx, fy, fw, fh in final_rects:
        # Check if centers are close
        if abs((x + w/2) - (fx + fw/2)) < width * 0.05:
            overlap = True
            break
    if not overlap:
        final_rects.append(r)
    if len(final_rects) == 4:
        break

final_rects.sort(key=lambda r: r[0]) # sort left to right

for i, r in enumerate(final_rects):
    x, y, w, h = r
    print(f"Window {i+1}: left={x/width*100:.1f}%, top={y/height*100:.1f}%, width={w/width*100:.1f}%, height={h/height*100:.1f}%")
