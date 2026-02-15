
<!-- Footer -->
<footer class="footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="footer-title">\u00c0 propos</h5>
                <p class="footer-text">
                    Blog moderne d\u00e9velopp\u00e9 avec PHP, MySQL et Bootstrap. 
                    Syst\u00e8me complet de gestion d'articles et de commentaires.
                </p>
                <div class="social-links">
                    <a href="#" class="btn-social"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="btn-social"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn-social"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="btn-social"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <h5 class="footer-title">Liens Rapides</h5>
                <ul class="footer-links">
                    <li><a href="index.php"><i class="fas fa-chevron-right me-2"></i>Accueil</a></li>
                    <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Articles</a></li>
                    <li><a href="login.php"><i class="fas fa-chevron-right me-2"></i>Admin</a></li>
                </ul>
            </div>
            
            <div class="col-lg-4 mb-4">
                <h5 class="footer-title">Contact</h5>
                <ul class="footer-contact">
                    <li><i class="fas fa-envelope me-2"></i>contact@blog.com</li>
                    <li><i class="fas fa-phone me-2"></i>+33 1 23 45 67 89</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i>Paris, France</li>
                </ul>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="footer-copyright mb-0">
                    &copy; <?= date('Y') ?> Blog Moderne. Tous droits r\u00e9serv\u00e9s.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="footer-copyright mb-0">
                    Fait avec <i class="fas fa-heart text-danger"></i> par SuperNinja
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Scroll to Top Button -->
<button id="scrollTop" class="scroll-top" onclick="scrollToTop()">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="includes/assets/js/script.js"></script>

</body>
</html>
