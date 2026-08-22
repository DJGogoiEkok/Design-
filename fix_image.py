from PIL import Image
import numpy as np
from skimage.morphology import flood_fill

# Original image (has white inside and outside)
img = Image.open("images/new_torn.png").convert("RGBA")
data = np.array(img)

# White threshold
is_white = (data[:,:,0] > 200) & (data[:,:,1] > 200) & (data[:,:,2] > 200)

mask = np.zeros(is_white.shape, dtype=np.uint8)
mask[is_white] = 1

# Flood fill INSIDE (center is 512,512) with 2
mask = flood_fill(mask, (512, 512), 2)

# Flood fill OUTSIDE (corner is 0,0) with 3
mask = flood_fill(mask, (0, 0), 3)

# Inside hole (2) -> transparent
inside = (mask == 2)
data[inside, 3] = 0

# Outside (3) -> match website background #fdfbf2 (253, 251, 242)
outside = (mask == 3)
data[outside, 0] = 253
data[outside, 1] = 251
data[outside, 2] = 242
data[outside, 3] = 255 # opaque

# Now crop to the red bounds (plus a small margin) so the frame is as big as possible
is_red = ~inside & ~outside
rows = np.any(is_red, axis=1)
cols = np.any(is_red, axis=0)
rmin, rmax = np.where(rows)[0][[0, -1]]
cmin, cmax = np.where(cols)[0][[0, -1]]

margin = 20
rmin = max(0, rmin - margin)
rmax = min(data.shape[0], rmax + margin)
cmin = max(0, cmin - margin)
cmax = min(data.shape[1], cmax + margin)

cropped_data = data[rmin:rmax, cmin:cmax]

Image.fromarray(cropped_data).save("images/torn_perfect.png")
print("Saved torn_perfect.png")
