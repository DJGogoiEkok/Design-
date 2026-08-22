from PIL import Image
import numpy as np
img = Image.open("images/new_torn.png").convert("RGBA").resize((64, 64))
data = np.array(img)
for r in range(64):
    row_str = ""
    for c in range(64):
        # if red > G and red > B, print R
        if data[r,c,0] > data[r,c,1] + 20 and data[r,c,0] > data[r,c,2] + 20:
            row_str += "R"
        elif data[r,c,0] > 220 and data[r,c,1] > 220 and data[r,c,2] > 220:
            row_str += "." # white
        else:
            row_str += "x" # shadow or other
    print(row_str)
