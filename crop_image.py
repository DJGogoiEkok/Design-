from PIL import Image

img = Image.open("images/torn_hole_transparent.png")
# Red bounds: top=116, bottom=906, left=75, right=926
# Let's add a small margin of 20 pixels
cropped = img.crop((55, 96, 946, 926))
cropped.save("images/torn_cropped.png")
print("Saved torn_cropped.png")
