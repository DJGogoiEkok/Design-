from PIL import Image
import numpy as np

img = Image.open("/Users/dj/.gemini/antigravity/brain/94942c90-c5c4-4baf-8f2d-0cff124e4b1d/.user_uploaded/media_1787328440000.png").convert("RGBA")
data = np.array(img)

# We want to make the center white area transparent.
# White is roughly R>220, G>220, B>220
r, g, b, a = data[:,:,0], data[:,:,1], data[:,:,2], data[:,:,3]

# Create a mask for white pixels
mask = (r > 200) & (g > 200) & (b > 200)

# But wait, there are shadows. And the outside is red (R ~220, G ~30, B ~30).
# So anything where G > 150 and B > 150 is definitely the white paper/shadow area.
mask = (g > 100) & (b > 100)

data[mask, 3] = 0 # Set alpha to 0

Image.fromarray(data).save("images/torn_frame.png")
print("Saved to images/torn_frame.png")
