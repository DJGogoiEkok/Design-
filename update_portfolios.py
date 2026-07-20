import re
import glob
import os

files_to_update = [
    'commercial.html',
    'interior.html',
    'residential.html',
    'others.html',
    'portfolio-details.html'
]

lightbox_code = """
<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox">
  <span class="lightbox-close">&times;</span>
  <img class="lightbox-content" id="lightbox-img">
</div>

<style>
.lightbox {
  visibility: hidden;
  position: fixed;
  z-index: 99999;
  left: 0; top: 0; width: 100%; height: 100%;
  background-color: rgba(0,0,0,0.9);
  backdrop-filter: blur(10px);
  opacity: 0;
  transition: opacity 0.3s ease, visibility 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.lightbox.show {
  visibility: visible;
  opacity: 1;
}
.lightbox-content {
  max-width: 90vw;
  max-height: 90vh;
  object-fit: contain;
  box-shadow: 0 0 50px rgba(0,0,0,0.8);
  transform: scale(0.9);
  transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.lightbox.show .lightbox-content {
  transform: scale(1);
}
.lightbox-close {
  position: absolute;
  top: 20px;
  right: 40px;
  color: #fff;
  font-size: 50px;
  font-weight: 300;
  cursor: pointer;
  transition: 0.2s;
  z-index: 2;
}
.lightbox-close:hover { color: var(--gold, #d4af37); transform: scale(1.1); }
.banner-item { cursor: pointer; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const closeBtn = document.querySelector('.lightbox-close');
  
  if(!lightbox || !lightboxImg || !closeBtn) return;

  document.querySelectorAll('.banner-item').forEach(banner => {
    banner.addEventListener('click', function() {
      const frontImg = this.querySelector('.img-front');
      const backImg = this.querySelector('.img-back');
      if(!frontImg) return;
      const frontOpacity = window.getComputedStyle(frontImg).opacity;
      if (parseFloat(frontOpacity) > 0.5) {
        lightboxImg.src = frontImg.src;
      } else {
        lightboxImg.src = backImg ? backImg.src : frontImg.src;
      }
      lightbox.classList.add('show');
    });
  });
  
  closeBtn.addEventListener('click', () => lightbox.classList.remove('show'));
  lightbox.addEventListener('click', (e) => {
    if (e.target !== lightboxImg) lightbox.classList.remove('show');
  });
});
</script>
"""

for fname in files_to_update:
    fpath = os.path.join('/Volumes/DJ_CRUCIAL/DEsign+/DP/redesign', fname)
    if not os.path.exists(fpath):
        print(f"Skipping {fname}, does not exist")
        continue
    
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Update section title
    # First, find section-title block
    section_title_match = re.search(r'<div class="section-title[^>]*>(.*?)</div>\s*<(div class="portfolio-grid|section class=")', content, re.DOTALL)
    
    if section_title_match:
        inner = section_title_match.group(1)
        
        # Replace eyebrow
        inner = re.sub(r'<div class="eyebrow">(.*?)</div>', r'<div class="eyebrow"><span class="sketch-highlight" style="font-size: 1rem; padding: 2px 10px;">\1</span></div>', inner)
        # Replace h2
        inner = re.sub(r'<h2>(.*?)</h2>', r'<h2 style="margin-top: 15px;"><span class="sketch-highlight">\1</span></h2>', inner)
        # Replace p
        inner = re.sub(r'<p>(.*?)</p>', r'<p style="margin-top: 15px;"><span class="sketch-highlight" style="padding: 5px 15px;">\1</span></p>', inner)
        
        # Wrap section-title in new style
        new_section_title = f'<div class="section-title reveal" style="text-align: center; margin-bottom: 50px;">{inner}</div>'
        
        # Replace old section title
        content = content[:section_title_match.start()] + new_section_title + '\n    ' + content[section_title_match.end(1):]
    
    # 2. Extract images from portfolio-grid or whatever container holds images
    img_matches = re.findall(r'<img src="(images/portfolio/[^"]+)" alt="([^"]+)"', content)
    
    # Deduplicate and keep order
    imgs = []
    for m in img_matches:
        if m not in imgs:
            imgs.append(m)
            
    if not imgs:
        print(f"No images found in {fname}")
        continue
        
    # Ensure at least 8 images by looping
    while len(imgs) < 8:
        imgs.extend(imgs)
    imgs = imgs[:8] # Take exactly 8
    
    banners_html = f"""<div class="hanging-banners-wrapper reveal">
      <div class="hanging-bg-text">Portfolio</div>
      <div class="hanging-bg-sub">Gallery</div>
"""
    for i in range(4):
        front_img = imgs[i]
        back_img = imgs[i+4]
        banners_html += f"""
      <div class="banner-item">
        <img src="{front_img[0]}" alt="{front_img[1]}" class="img-front">
        <img src="{back_img[0]}" alt="{back_img[1]}" class="img-back">
        <div class="overlay"><h4>{front_img[1]}</h4></div>
      </div>
"""
    banners_html += "    </div>"
    
    # Replace portfolio-grid with banners
    # Try to find portfolio-grid
    grid_match = re.search(r'<div class="portfolio-grid[^>]*>.*?</div>\s*</div>\s*</section>', content, re.DOTALL)
    if grid_match:
        content = content[:grid_match.start()] + banners_html + '\n  </div>\n</section>' + content[grid_match.end():]
    else:
        # Just find where to inject, maybe it's just <div class="portfolio-grid ...> ... </div>
        grid_match = re.search(r'<div class="portfolio-grid[^>]*>.*?</div>\s*</div>', content, re.DOTALL)
        if grid_match:
            content = content[:grid_match.start()] + banners_html + '\n  </div>' + content[grid_match.end():]
    
    # 3. Inject lightbox if not present
    if 'id="lightbox"' not in content:
        content = content.replace('<script src="js/main.js?v=35"></script>', lightbox_code + '\n<script src="js/main.js?v=35"></script>')
        content = content.replace('<script src="js/main.js"></script>', lightbox_code + '\n<script src="js/main.js"></script>')
        
    with open(fpath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated {fname}")

