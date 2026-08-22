from PIL import Image
import numpy as np

img = Image.open("images/new_torn.png").convert("RGBA")
data = np.array(img)

# Find where it's NOT white (the red parts)
# White is roughly > 240
is_not_white = (data[:,:,0] < 240) | (data[:,:,1] < 240) | (data[:,:,2] < 240)

# Find the bounding box of the red parts
rows = np.any(is_not_white, axis=1)
cols = np.any(is_not_white, axis=0)

rmin, rmax = np.where(rows)[0][[0, -1]]
cmin, cmax = np.where(cols)[0][[0, -1]]

print(f"Red bounds: top={rmin}, bottom={rmax}, left={cmin}, right={cmax}")

# Now find the hole inside the red bounds
# The hole is the white part INSIDE the red bounds.
# Let's just sample the middle and see how far white goes.
center_r, center_c = 512, 512
# Walk left
left = center_c
while left > 0 and data[center_r, left, 0] > 240:
    left -= 1
# Walk right
right = center_c
while right < 1024 and data[center_r, right, 0] > 240:
    right += 1
# Walk up
top = center_r
while top > 0 and data[top, center_c, 0] > 240:
    top -= 1
# Walk down
bottom = center_r
while bottom < 1024 and data[bottom, center_c, 0] > 240:
    bottom += 1

print(f"Hole bounds at center: top={top}, bottom={bottom}, left={left}, right={right}")
