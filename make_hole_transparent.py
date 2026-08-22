from PIL import Image
import numpy as np
from skimage.morphology import flood_fill

# Open image
img = Image.open("images/new_torn.png").convert("RGBA")
data = np.array(img)

# We want to flood fill the center white area.
# Let's create a mask of "white" pixels.
# White is roughly R>200, G>200, B>200.
is_white = (data[:,:,0] > 200) & (data[:,:,1] > 200) & (data[:,:,2] > 200)

# We'll use skimage's flood_fill on a boolean mask.
# Center pixel is (512, 512).
# We want to fill all connected "True" pixels starting from center.
mask = np.zeros(is_white.shape, dtype=np.uint8)
mask[is_white] = 1

# flood_fill(image, seed_point, new_value)
filled_mask = flood_fill(mask, (512, 512), 2)

# filled_mask now has 2 where the center hole is.
# Set those pixels to transparent in the original data.
hole = (filled_mask == 2)
data[hole, 3] = 0 # alpha = 0

# Save the new image
Image.fromarray(data).save("images/torn_hole_transparent.png")
print("Saved torn_hole_transparent.png")
