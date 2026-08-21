from PIL import Image
import numpy as np

img = Image.open("images/torn_original.png").convert("RGBA")
data = np.array(img)

r, g, b, a = data[:,:,0], data[:,:,1], data[:,:,2], data[:,:,3]

# The red is around R=234, G=56, B=52.
# We want to make any pixel that is predominantly red transparent.
# A pixel is red if R > G + 50 and R > B + 50.
# But wait, the shadows cast on the red background will be darker red.
# If we just look at hue...
# Let's just say if R > G * 1.5 and R > B * 1.5, it's red.
is_red = (r > g * 1.5) & (r > b * 1.5) & (r > 100)

data[is_red, 3] = 0 # Make red transparent

# Save it to see how it looks.
Image.fromarray(data).save("images/torn_no_red.png")
print("Saved torn_no_red.png")
