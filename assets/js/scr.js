        // Function to open image popup
        function openPopup(filename, username, likeCount, liked, imageId, judul, tagar) {
            const popup = document.getElementById('popup');
            const popupImg = document.getElementById('popup-img');
            const popupUsername = document.getElementById('popup-username');
            const popupLikeCount = document.getElementById('popup-like-count');
            const popupLikeIcon = document.getElementById('popup-like-icon');
            const popupLikeButton = document.querySelector('.popup-content .btn-like');
            const saveButton = document.querySelector('.popup-content .btn-save');
            
            // Set image source and details
            popupImg.src = 'uploads/' + filename;
            popupUsername.textContent = 'Uploaded by: ' + username;
            popupLikeCount.textContent = likeCount;
        
            // Display title and hashtags
            const popupTitle = document.getElementById('popup-title');
            const popupTags = document.getElementById('popup-tags');
            popupTitle.textContent = 'Title: ' + judul;
            popupTags.textContent = 'Tags: ' + tagar;
        
            // Update like button icon based on liked status
            if (liked) {
                popupLikeIcon.classList.remove('fa-heart-o');
                popupLikeIcon.classList.add('fa-heart');
            } else {
                popupLikeIcon.classList.remove('fa-heart');
                popupLikeIcon.classList.add('fa-heart-o');
            }
        
            // Attach like event to the like button
            popupLikeButton.setAttribute('data-image-id', imageId);
            popupLikeButton.onclick = () => likeImage(imageId);
        
            // Attach save event to the save button
            saveButton.setAttribute('data-image-id', imageId);
            saveButton.onclick = () => saveImage(imageId);
        
            // Fetch comments for this image
            fetch(`fetch_comments.php?image_id=${imageId}`)
            
        
        .then(response => response.json())
        .then(comments => {
            const commentsContainer = document.getElementById('comments-container');
            commentsContainer.innerHTML = ''; // Clear previous comments
            comments.forEach(comment => {
            const commentElement = document.createElement('div');
            commentElement.classList.add('comment-item');
            commentElement.innerHTML = `<strong>${comment.username}</strong>: ${comment.comment}`;
            commentsContainer.appendChild(commentElement);
        });
        
        })
        .catch(error => console.error('Error fetching comments:', error));
        
        
            popup.style.display = 'flex';
        }
        
        
        
        // Function to close image popup
        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
        
        // Function to delete image
        function deleteImage(imageId) {
            if (confirm("Are you sure you want to delete this image?")) {
                fetch('delete.php?id=' + imageId, {
                    method: 'GET',
                })
                .then(response => {
                    if (response.ok) {
                        location.reload();
                    } else {
                        alert("Failed to delete the image.");
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
        
        // Function to create notification
        function createNotification(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
        
            // Hide notification after 3 seconds
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
        
        // Function to like image
        function likeImage(imageId) {
            fetch('like.php?id=' + imageId, {
                method: 'POST',
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const likeButton = document.querySelector(`.btn-like[data-image-id="${imageId}"]`);
                    const heartIcon = likeButton.querySelector('i');
                    const likeCount = document.getElementById('popup-like-count');
        
                    if (data.liked) {
                        heartIcon.classList.remove('fa-heart-o');
                        heartIcon.classList.add('fa-heart');
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else {
                        heartIcon.classList.remove('fa-heart');
                        heartIcon.classList.add('fa-heart-o');
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        // Function to save image
        function saveImage(imageId) {
            console.log('Saving image with ID:', imageId);
        
            fetch('save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({ id: imageId })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Server response:', data);
        
                if (data.success) {
                    // Update tombol Save/Unsaved di menu titik tiga
                    const saveButton = document.querySelector(`.menu-popup button[onclick="saveImage(${imageId})"]`);
                    if (saveButton) {
                        saveButton.textContent = data.saved ? 'Saved' : 'Save';
                    }
        
                    // Update tombol di popup jika diperlukan
                    const popupSaveButton = document.querySelector(`.popup-content .btn-save[data-image-id="${imageId}"]`);
                    if (popupSaveButton) {
                        popupSaveButton.textContent = data.saved ? 'Saved' : 'Save';
                    }
        
                    // Tampilkan notifikasi sukses
                    createNotification(data.saved ? 'Image saved successfully!' : 'Image unsaved.');
                } else {
                    console.error('Failed to save the image:', data.message);
                    createNotification('Failed to save image.');
                }
            })
            .catch(error => {
                console.error('Error saving image:', error);
                createNotification('Error saving image.');
            });
        }
        
        
        
        
        // Function to toggle profile menu
        function toggleProfileMenu(event) {
            event.preventDefault();
            const dropdownMenu = document.getElementById('profile-dropdown');
            dropdownMenu.style.display = (dropdownMenu.style.display === "block") ? "none" : "block";
        }
        
        // Close profile dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.profile-image')) {
                const dropdown = document.getElementById("profile-dropdown");
                if (dropdown.style.display === "block") {
                    dropdown.style.display = "none";
                }
            }
        };
        
        // Function to toggle menu popup
        function toggleMenu(imageId) {
            const menu = document.getElementById(`menu-popup-${imageId}`);
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        }
        
        // Handle like click
        function handleLikeClick(imageId) {
            if (isLoggedIn) {
                likeImage(imageId);
            } else {
                alert("You must log in to like this image.");
            }
        }
        