from PIL import Image
import numpy as np
img = Image.open("images/torn_cropped.png")
data = np.array(img)
# The hole is where alpha == 0
hole = (data[:,:,3] == 0)
rows = np.any(hole, axis=1)
cols = np.any(hole, axis=0)
rmin, rmax = np.where(rows)[0][[0, -1]]
cmin, cmax = np.where(cols)[0][[0, -1]]
height, width = data.shape[0], data.shape[1]
print(f"Image size: {width}x{height}")
print(f"Hole bounds: top={rmin}, bottom={rmax}, left={cmin}, right={cmax}")
print(f"Padding % needed: top={rmin/height*100:.1f}%, bottom={(height-rmax)/height*100:.1f}%, left={cmin/width*100:.1f}%, right={(width-cmax)/width*100:.1f}%")
