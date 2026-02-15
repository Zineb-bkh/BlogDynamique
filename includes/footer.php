<!-- Footer -->
<footer class="footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5 class="footer-title">À propos</h5>
                <p class="footer-text">
                    Blog moderne développé avec PHP, MySQL et Bootstrap. 
                    Système complet de gestion d'articles et de commentaires.
                </p>
                <div class="social-links">
                    <a href="mailto:zm179151@gmail.com" class="btn-social"><i class="fas fa-envelope"></i></a>
                    <a href="https://github.com/Zineb-bkh" class="btn-social" target="_blank"><i class="fab fa-github"></i></a>
                    <a href="https://www.linkedin.com/in/zineb-boukhou-796775335?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" class="btn-social"><i class="fab fa-linkedin-in"></i></a>
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
                    <li><i class="fas fa-envelope me-2"></i>zm179151@gmail.com</li>
                    <li><i class="fas fa-phone me-2"></i>+212 627 73 05 08</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i>Laayoune, Maroc</li>
                </ul>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="footer-copyright mb-0">
                    &copy; <?= date('Y') ?> Blog Moderne. Tous droits réservés.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="footer-copyright mb-0">
                    Fait avec <i class="fas fa-heart text-danger"></i> Zineb Boukhou.
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