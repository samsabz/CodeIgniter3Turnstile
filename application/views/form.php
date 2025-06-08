<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>Cloudflare Turnstile</title>
</head>
<body>
    <h1>My Form</h1>
    <?php if ($this->session->flashdata('error')): ?>
        <p style="color: red;"><?php echo $this->session->flashdata('error'); ?></p>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
        <p style="color: green;"><?php echo $this->session->flashdata('success'); ?></p>
    <?php endif; ?>
    
    <form action="<?php echo base_url('your_controller/submit'); ?>" method="POST">
        <label for="name">Name :</label>
        <input type="text" name="name" id="name" required><br><br>

        <!-- نمایش ویجت Turnstile -->
        <?php echo $turnstile_widget; ?>

        <button type="submit">Submit</button>
    </form>
</body>
</html>