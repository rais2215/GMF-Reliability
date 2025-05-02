// Script for Sidebar Report
const sidebarItems = document.querySelectorAll('.sidebar-item');

        sidebarItems.forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();

                const url = this.getAttribute('data-url');
                const mainContent = document.getElementById('main-content');

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        mainContent.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error fetching content:', error);
                        mainContent.innerHTML = '<p>Error loading content. Please try again later.</p>';
                    });
            });
        });
