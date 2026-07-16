<?php
if (!isset($service)) {
  header('Location: index.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($service['meta_title']); ?></title>

    <meta name="description" content="<?php echo htmlspecialchars($service['meta_description']); ?>">

    <meta name="keywords" content="<?php echo htmlspecialchars($service['keywords']); ?>">

    <meta name="author" content="Balancing Carbon">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2d5730">

    <link rel="canonical" href="https://www.balancingcarbon.com/<?php echo htmlspecialchars($service['slug']); ?>.php">

    <!-- Open Graph -->
    <meta property="og:type" content="website">

    <meta property="og:title" content="<?php echo htmlspecialchars($service['meta_title']); ?>">

    <meta property="og:description" content="<?php echo htmlspecialchars($service['meta_description']); ?>">

    <meta property="og:url"
        content="https://www.balancingcarbon.com/<?php echo htmlspecialchars($service['slug']); ?>.php">

    <meta property="og:image" content="https://www.balancingcarbon.com/assets/images/social-preview.jpg">

    <meta property="og:site_name" content="Balancing Carbon">

    <!-- Twitter/X -->
    <meta name="twitter:card" content="summary_large_image">

    <meta name="twitter:title" content="<?php echo htmlspecialchars($service['meta_title']); ?>">

    <meta name="twitter:description" content="<?php echo htmlspecialchars($service['meta_description']); ?>">

    <meta name="twitter:image" content="https://www.balancingcarbon.com/assets/images/social-preview.jpg">

    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">

    <script src="assets/js/custom.js" defer></script>
</head>

<body>
    <?php include 'header.php'; ?>

    <main>
        <!-- =============================================
         SERVICE BANNER
    ============================================== -->

        <section class="service-page-banner">
            <div class="service-banner-shape service-shape-one"></div>
            <div class="service-banner-shape service-shape-two"></div>

            <div class="service-banner-container">
                <div class="service-banner-content">
                    <nav class="service-breadcrumb" aria-label="Breadcrumb">
                        <a href="index.php">Home</a>
                        <span aria-hidden="true">/</span>
                        <a href="services.php">Services</a>
                        <span aria-hidden="true">/</span>

                        <span>
                            <?php echo htmlspecialchars($service['title']); ?>
                        </span>
                    </nav>

                    <span class="service-banner-label">
                        <?php echo htmlspecialchars($service['label']); ?>
                    </span>

                    <h1>
                        <?php echo htmlspecialchars($service['title']); ?>
                    </h1>

                    <p>
                        <?php echo htmlspecialchars($service['hero_description']); ?>
                    </p>

                    <div class="service-banner-actions">
                        <a href="contact-us.php" class="service-banner-primary">
                            Discuss Your Requirements

                            <svg viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M4 10H16"></path>
                                <path d="M11 5L16 10L11 15"></path>
                            </svg>
                        </a>

                        <a href="#service-overview" class="service-banner-secondary">
                            Explore Service
                        </a>
                    </div>
                </div>

                <!-- Decorative service visual -->
                <div class="service-banner-visual" aria-hidden="true">
                    <div class="service-orbit orbit-one"></div>
                    <div class="service-orbit orbit-two"></div>

                    <div class="service-visual-icon">
                        <svg viewBox="0 0 80 80">
                            <circle cx="40" cy="40" r="29"></circle>
                            <path d="M14 40H66"></path>
                            <path d="M40 11C29 21 25 31 25 40C25 49 29 59 40 69"></path>
                            <path d="M40 11C51 21 55 31 55 40C55 49 51 59 40 69"></path>
                            <path d="M20 25H60"></path>
                            <path d="M20 55H60"></path>
                        </svg>
                    </div>

                    <span class="visual-node node-one"></span>
                    <span class="visual-node node-two"></span>
                    <span class="visual-node node-three"></span>

                    <div class="service-visual-label">
                        <span><?php echo htmlspecialchars($service['number']); ?></span>

                        <strong>
                            <?php echo htmlspecialchars($service['short_title']); ?>
                        </strong>
                    </div>
                </div>
            </div>
        </section>

        <!-- =============================================
         SERVICE OVERVIEW
    ============================================== -->

        <section class="service-overview-section" id="service-overview">
            <div class="service-overview-container">
                <div class="service-overview-content">
                    <div class="content-label">
                        <span></span>
                        Service Overview
                    </div>

                    <h2>
                        <?php echo htmlspecialchars($service['overview_title']); ?>
                    </h2>

                    <?php foreach ($service['overview_paragraphs'] as $paragraph): ?>
                    <p>
                        <?php echo htmlspecialchars($paragraph); ?>
                    </p>
                    <?php endforeach; ?>

                    <a href="contact-us.php" class="service-primary-button">
                        Start a Conversation

                        <svg viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M4 10H16"></path>
                            <path d="M11 5L16 10L11 15"></path>
                        </svg>
                    </a>
                </div>

                <div class="service-overview-panel">
                    <span class="overview-panel-label">What this service covers</span>

                    <ul>
                        <?php foreach ($service['coverage'] as $item): ?>
                        <li>
                            <span aria-hidden="true">
                                <svg viewBox="0 0 20 20">
                                    <path d="M4 10L8 14L16 6"></path>
                                </svg>
                            </span>

                            <?php echo htmlspecialchars($item); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>

        <!-- =============================================
         SERVICE OFFERINGS
    ============================================== -->

        <section class="service-offerings-section">
            <div class="service-offerings-container">
                <div class="service-section-heading">
                    <div class="content-label centered-label">
                        <span></span>
                        What We Offer
                        <span></span>
                    </div>

                    <h2>
                        <?php echo htmlspecialchars($service['offerings_title']); ?>
                    </h2>

                    <p>
                        <?php echo htmlspecialchars($service['offerings_description']); ?>
                    </p>
                </div>

                <div class="service-offerings-grid">
                    <?php foreach ($service['offerings'] as $index => $offering): ?>
                    <article class="service-offering-card">
                        <span class="offering-number">
                            <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                        </span>

                        <div class="offering-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 19V11"></path>
                                <path d="M10 19V5"></path>
                                <path d="M16 19V9"></path>
                                <path d="M22 19V3"></path>
                            </svg>
                        </div>

                        <h3>
                            <?php echo htmlspecialchars($offering['title']); ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($offering['description']); ?>
                        </p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =============================================
         BUSINESS VALUE
    ============================================== -->

        <section class="service-value-section">
            <div class="service-value-container">
                <div class="service-value-heading">
                    <div class="content-label">
                        <span></span>
                        Business Value
                    </div>

                    <h2>
                        <?php echo htmlspecialchars($service['value_title']); ?>
                    </h2>

                    <p>
                        <?php echo htmlspecialchars($service['value_description']); ?>
                    </p>
                </div>

                <div class="service-value-list">
                    <?php foreach ($service['benefits'] as $index => $benefit): ?>
                    <article class="service-value-item">
                        <span class="value-item-number">
                            <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                        </span>

                        <div>
                            <h3>
                                <?php echo htmlspecialchars($benefit['title']); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($benefit['description']); ?>
                            </p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =============================================
         OUR PROCESS
    ============================================== -->

        <section class="service-process-section">
            <div class="service-process-container">
                <div class="service-section-heading">
                    <div class="content-label centered-label">
                        <span></span>
                        Our Approach
                        <span></span>
                    </div>

                    <h2>A Clear and Structured Process</h2>

                    <p>
                        The exact scope is tailored to your organisation, available
                        information and reporting requirements.
                    </p>
                </div>

                <div class="service-process-grid">
                    <?php foreach ($service['process'] as $index => $step): ?>
                    <article class="service-process-step">
                        <span class="process-step-number">
                            <?php echo str_pad($index + 1, 2, '0', STR_PAD_LEFT); ?>
                        </span>

                        <h3>
                            <?php echo htmlspecialchars($step['title']); ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($step['description']); ?>
                        </p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =============================================
         OTHER SERVICES
    ============================================== -->

        <section class="related-services-section">
            <div class="related-services-container">
                <div class="related-services-heading">
                    <div>
                        <div class="content-label">
                            <span></span>
                            Related Services
                        </div>

                        <h2>Explore Our Other Capabilities</h2>
                    </div>

                    <a href="services.php">View all services →</a>
                </div>

                <div class="related-services-grid">
                    <?php foreach ($service['related'] as $related): ?>
                    <a href="<?php echo htmlspecialchars($related['url']); ?>" class="related-service-card">
                        <span>
                            <?php echo htmlspecialchars($related['number']); ?>
                        </span>

                        <h3>
                            <?php echo htmlspecialchars($related['title']); ?>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($related['description']); ?>
                        </p>

                        <strong>Discover service →</strong>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- =============================================
         SERVICE CTA
    ============================================== -->

        <section class="service-page-cta">
            <div class="service-page-cta-container">
                <div>
                    <div class="content-label service-light-label">
                        <span></span>
                        Start a Conversation
                    </div>

                    <h2>
                        <?php echo htmlspecialchars($service['cta_title']); ?>
                    </h2>

                    <p>
                        Tell us about your requirements and begin a conversation
                        with Balancing Carbon.
                    </p>
                </div>

                <a href="contact-us.php" class="service-cta-button">
                    Contact Us

                    <svg viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M4 10H16"></path>
                        <path d="M11 5L16 10L11 15"></path>
                    </svg>
                </a>
            </div>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>

</html>