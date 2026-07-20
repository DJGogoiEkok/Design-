document.addEventListener('DOMContentLoaded', function() {
    // Create modal element
    const modalHtml = `
        <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
              <div class="modal-body text-center position-relative">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="background-color: white; z-index: 1050;"></button>
                <img src="" id="modalImage" class="img-fluid rounded shadow" style="max-height: 85vh; object-fit: contain; display: none;">
                <video src="" id="modalVideo" class="img-fluid rounded shadow" style="max-height: 85vh; object-fit: contain; display: none;" controls autoplay loop></video>
              </div>
            </div>
          </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Initialize Bootstrap Modal
    const modalEl = document.getElementById('imageModal');
    let bsModal;
    if (typeof bootstrap !== 'undefined') {
        bsModal = new bootstrap.Modal(modalEl);
    }

    // Stop video when modal closes
    modalEl.addEventListener('hidden.bs.modal', function () {
        document.getElementById('modalVideo').pause();
    });

    // Add click event to all thumbnails (images and videos)
    const thumbnails = document.querySelectorAll('.img-thumbnail-popup, video[autoplay]');
    thumbnails.forEach(media => {
        media.style.cursor = 'pointer';
        media.addEventListener('click', function() {
            let fullSrc = this.src || this.currentSrc;
            let modalImg = document.getElementById('modalImage');
            let modalVid = document.getElementById('modalVideo');
            
            if (this.tagName.toLowerCase() === 'video') {
                modalImg.style.display = 'none';
                modalVid.style.display = 'block';
                modalVid.src = fullSrc;
                modalVid.play();
            } else {
                modalVid.style.display = 'none';
                modalImg.style.display = 'block';
                modalImg.src = fullSrc;
            }
            
            if (bsModal) bsModal.show();
        });
    });
});

// Testimonials edit modal logic
document.addEventListener('DOMContentLoaded', function() {
    const editTestimonialBtns = document.querySelectorAll('.edit-testimonial-btn');
    if (editTestimonialBtns.length > 0) {
        editTestimonialBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const author = this.getAttribute('data-author');
                const quote = this.getAttribute('data-quote');
                
                document.getElementById('edit-testimonial-id').value = id;
                document.getElementById('edit-testimonial-author').value = author;
                document.getElementById('edit-testimonial-quote').value = quote;
                
                const editModal = new bootstrap.Modal(document.getElementById('editTestimonialModal'));
                editModal.show();
            });
        });
    }
});
