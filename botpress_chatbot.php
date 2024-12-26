<style>
         footer {
            position: relative;
            background-color: #f1f1f1;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #ccc;
        }

        /* AI Icon Button */
        #chatIcon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            background-color: #0078d4; /* Button background color */
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        #chatIcon i {
            margin-right: 10px; /* Space between icon and text */
            font-size: 18px;
        }

        /* Chat iframe */
        #chatFrame {
            position: fixed;
            bottom: 80px;
            right: 20px;
            width: 400px;
            height: 500px;
            border: none;
            display: none; /* Initially hidden */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1000;
        }
    </style>
<!-- End of Main Content -->
   
    <!-- Footer -->
    <footer>
        &copy; 2024 Your Website Name. All Rights Reserved.
    </footer>

    <!-- Chat Button -->
    <button id="chatIcon">
        <i class="fas fa-robot"></i> <!-- FontAwesome AI Icon -->
        Ask me a Question
    </button>

    <!-- Chat iframe -->
    <iframe 
        id="chatFrame"
        src="https://cdn.botpress.cloud/webchat/v2.2/shareable.html?configUrl=https://files.bpcontent.cloud/2024/12/16/07/20241216070618-XM1CWM86.json">
    </iframe>

    <script>
        // JavaScript to toggle chat iframe visibility
        document.getElementById('chatIcon').addEventListener('click', function() {
            const chatFrame = document.getElementById('chatFrame');
            if (chatFrame.style.display === 'none' || chatFrame.style.display === '') {
                chatFrame.style.display = 'block'; // Show chat
            } else {
                chatFrame.style.display = 'none'; // Hide chat
            }
        });
    </script>
            <!-- End of Footer -->
