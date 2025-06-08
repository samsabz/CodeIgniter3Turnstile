# CodeIgniter 3 Cloudflare Turnstile Library

A lightweight library to integrate **Cloudflare Turnstile** CAPTCHA into **CodeIgniter 3** applications. This library simplifies the process of rendering the Turnstile widget and validating user responses on the server side.

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Rendering the Widget](#rendering-the-widget)
  - [Validating the Response](#validating-the-response)
- [Example](#example)
- [Contributing](#contributing)
- [License](#license)

## Features
- Easy integration of Cloudflare Turnstile in CodeIgniter 3 projects.
- Configurable widget options (e.g., theme, size, callback).
- Secure server-side validation using Cloudflare's API.
- Supports configuration via CodeIgniter's config file.
- Lightweight and reusable code.

## Requirements
- PHP 5.6 or higher (compatible with CodeIgniter 3 requirements).
- CodeIgniter 3.x.
- cURL extension enabled for server-side validation.
- A Cloudflare account with Turnstile enabled.
- Cloudflare Turnstile Site Key and Secret Key.

## Installation
1. **Download or Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/codeigniter3-turnstile.git
   ```

2. **Copy the Library**:
   - Copy the `Turnstile.php` file from the repository to your CodeIgniter project's `application/libraries` directory.

3. **Set Up Cloudflare Turnstile**:
   - Log in to your Cloudflare dashboard.
   - Navigate to **Turnstile** and create a new site.
   - Obtain your **Site Key** and **Secret Key**.

## Configuration
1. **Add Keys to Config**:
   Open `application/config/config.php` and add your Turnstile keys:
   ```php
   $config['turnstile_site_key'] = 'YOUR_SITE_KEY';
   $config['turnstile_secret_key'] = 'YOUR_SECRET_KEY';
   ```
   Replace `YOUR_SITE_KEY` and `YOUR_SECRET_KEY` with the keys provided by Cloudflare.

2. **Alternative Configuration**:
   You can also pass the keys directly when loading the library (see [Usage](#usage)).

## Usage
### Rendering the Widget
1. Load the library in your controller:
   ```php
   $this->load->library('turnstile');
   ```

2. Generate the Turnstile widget HTML:
   ```php
   $data['turnstile_widget'] = $this->turnstile->get_widget([
       'theme' => 'light', // Options: light, dark, auto
       'size' => 'normal'  // Options: normal, compact
   ]);
   ```

3. Pass the widget to your view:
   ```php
   $this->load->view('your_form', $data);
   ```

4. In your view (`application/views/your_form.php`), display the widget:
   ```html
   <form action="<?php echo base_url('your_controller/submit'); ?>" method="POST">
       <label for="name">Name:</label>
       <input type="text" name="name" id="name" required><br><br>
       <?php echo $turnstile_widget; ?>
       <button type="submit">Submit</button>
   </form>
   ```

### Validating the Response
1. In your controller, validate the Turnstile response:
   ```php
   public function submit() {
       $turnstile_response = $this->input->post('cf-turnstile-response');
       if ($this->turnstile->validate($turnstile_response, $this->input->ip_address())) {
           $this->session->set_flashdata('success', 'Form submitted successfully!');
       } else {
           $this->session->set_flashdata('error', 'Robot verification failed. Please try again.');
       }
       redirect('your_controller');
   }
   ```

## Example
Below is a complete example of a controller and view using the Turnstile library.

### Controller (`application/controllers/Your_controller.php`)
```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Your_controller extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('turnstile');
    }

    public function index() {
        $data['turnstile_widget'] = $this->turnstile->get_widget(['theme' => 'light']);
        $this->load->view('your_form', $data);
    }

    public function submit() {
        $turnstile_response = $this->input->post('cf-turnstile-response');
        if ($this->turnstile->validate($turnstile_response, $this->input->ip_address())) {
            $this->session->set_flashdata('success', 'Form submitted successfully!');
        } else {
            $this->session->set_flashdata('error', 'Robot verification failed. Please try again.');
        }
        redirect('your_controller');
    }
}
```

### View (`application/views/your_form.php`)
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form with Cloudflare Turnstile</title>
</head>
<body>
    <h1>Test Form</h1>
    <?php if ($this->session->flashdata('error')): ?>
        <p style="color: red;"><?php echo $this->session->flashdata('error'); ?></p>
    <?php endif; ?>
    <?php if ($this->session->flashdata('success')): ?>
        <p style="color: green;"><?php echo $this->session->flashdata('success'); ?></p>
    <?php endif; ?>
    
    <form action="<?php echo base_url('your_controller/submit'); ?>" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br><br>
        <?php echo $turnstile_widget; ?>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
```

## Contributing
Contributions are welcome! Please feel free to submit a Pull Request or open an Issue on GitHub to suggest improvements or report bugs.

## License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.