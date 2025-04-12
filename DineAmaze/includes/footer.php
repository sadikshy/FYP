<style>
    footer {
        background-color: #333;
        color: white;
        padding: 30px 0 10px;
        margin-top: 50px;
    }

    .footer-content {
        display: flex;
        justify-content: space-around;
        align-items: center;
        flex-wrap: wrap;
        
        max-width: 100%;
        margin: 0 auto;
        padding: 0 20px;
    }

    .nav-footer, .contact-footer {
        margin-bottom: 20px;
    }

    .nav-footer h3, .contact-footer h3 {
        font-size: 18px;
        margin-bottom: 15px;
        color: #4CAF50;
    }

    .nav-links a {
        color: #ddd;
        text-decoration: none;
        transition: color 0.3s;
        margin-top: 10px;

    }

    .nav-links a:hover {
        color: #4CAF50;
    }

    .contact-footer p {
        margin-bottom: 8px;
        color: #ddd;
    }

    .footer-bottom {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #444;
        margin-top: 20px;
    }

    .footer-bottom p {
        font-size: 14px;
        color: #aaa;
    }
    .nav-links{
        display: flex;
        justify-content: center;
        flex-direction: column;
        align-items: center;
        ;
    }
    /* Responsive Design */
    @media (max-width: 768px) {
        .footer-content {
            flex-direction: column;
            text-align: center;
        }
        
        .nav-footer, .contact-footer {
            width: 100%;
        }
    }
</style>

<footer>
    <div class="footer-content">
        <div class="nav-footer">
            <h3>Navigation</h3>
            <div class="nav-links">
                <a href="Homepage.php">Home</a> 
                <a href="AboutUs.php">About Us</a> 
                <a href="Menu.php">Menu</a> 
                <a href="Customization.php">Customization</a> 
                <a href="Takeout.php">TakeOut</a> 
                <a href="ContactUs.php">Contact Us</a> 
                <a href="account_settings.php">My Account</a>
            </div>
        </div>
        <div class="contact-footer" id="contact">
            <h3>Contact Us</h3>
            <p>Email: DineAmaze@gmail.com</p>
            <p>Phone: 9861050118, 016675486</p>
            <p>Address: Srijananagar, Bhaktapur</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 DineAmaze. All rights reserved.</p>
    </div>
</footer>