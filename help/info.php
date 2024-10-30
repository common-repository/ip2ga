<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="ga-ip2c-instructions">
    <h1>IP2GA</h1>
    <p>Track all user activities on the site, including page views, button clicks, and form submissions, and send them to Google Analytics 4.</p>

    <h2>Description</h2>
    <p>The IP2GA is designed to capture and send comprehensive user interaction data to Google Analytics 4 (GA4). It automatically tracks various user activities on your site, such as page views, clicks, form submissions, and more. Additionally, it retrieves company data based on the visitor's IP address using the IP2C API and integrates this information into your GA4 events, allowing for more detailed and customized tracking.</p>

    <h3>Features</h3>
    <ul>
        <li>Tracks and sends various user interactions (page views, button clicks, form submissions, etc.) to Google Analytics 4.</li>
        <li>Retrieves company data based on the visitor's IP address and includes it in GA4 events.</li>
        <li>Supports tracking for outbound links, file downloads, scroll depth, video interactions, and more.</li>
        <li>Automatically handles different traffic sources and user agents for accurate reporting, including UTM parameters for campaign tracking.</li>
        <li>Provides a settings page to configure the IP2C API Token, GA4 Tracking ID, and GA4 API Secret.</li>
    </ul>

    <h3>Video instructions for setting up GA4</h3>
    <a href="https://youtu.be/D-8aoIpLn9E" target="_blank" style="text-decoration:none;">
        <button class="button button-primary">
            Watch Video Tutorial
        </button>
    </a>
    <p>For more detailed setup instructions, you can watch the video tutorial on YouTube.</p>

<h2>Subscribing to the IP2Company API on RapidAPI</h2>
<p>To use the IP2Company service for retrieving company information based on the visitor’s IP address, you need to subscribe to the API provided by Wiredminds on RapidAPI. The API offers a free plan that you can use for testing and getting familiar with the service before upgrading to a paid plan if necessary.</p>

<h3>Steps to Subscribe to IP2Company API</h3>
<ol>
    <li>
        <strong>Create an account on RapidAPI:</strong>
        <p>Go to <a href="https://rapidapi.com/" target="_blank">RapidAPI</a> and click on the "Sign Up" button in the top right corner. You can sign up using your email, Google account, or GitHub account.</p>
    </li>
    <li>
        <strong>Subscribe to the IP2Company API:</strong>
        <p>Once you're signed in, go directly to the <a href="https://rapidapi.com/wiredminds-gmbh-wiredminds-gmbh-default/api/ip2company3" target="_blank">IP2Company API Subscription Page</a> and select a plan. There is a free version available that you can use for testing and familiarization purposes.</p>
    </li>
    <li>
        <strong>Retrieve your API key:</strong>
        <p>After subscribing, go to the "Endpoints" tab on the IP2Company API page. There, you'll find your <strong>RapidAPI Key</strong>. This key is required to access the API from the plugin.</p>
    </li>
    <li>
        <strong>Enter the API key in the plugin settings:</strong>
        <p>Go to your WordPress admin dashboard, navigate to <strong>Settings</strong> -> <strong>GA IP2C Settings</strong>, and paste your <strong>RapidAPI Key</strong> into the "RapidAPI Token" field. This will enable the plugin to retrieve company data based on the IP address of your site visitors.</p>
    </li>
</ol>

<p><strong>Note:</strong> The free version of the IP2Company API is designed for testing purposes. It allows you to familiarize yourself with the API and its features. If you require more frequent use or additional features, you can upgrade to a paid plan at any time.</p>

<a href="<?php echo esc_url(plugins_url('ip2c-rapid.png', __FILE__)); ?>" target="_blank">
    <img src="<?php echo esc_url(plugins_url('ip2c-rapid.png', __FILE__)); ?>" alt="Example of subscribing to IP2Company API on RapidAPI" style="width:100%; max-width:600px;">
</a>

<p>For more details on the available plans and pricing, you can visit the <a href="https://rapidapi.com/wiredminds-gmbh-wiredminds-gmbh-default/api/ip2company3" target="_blank">IP2Company API page on RapidAPI</a>.</p>

<h2>Setting Up Google Analytics 4</h2>

<h3>1. Create a Measurement ID and API Secret in GA4</h3>
<ol>
    <li>
        <strong>Open your Google Analytics 4 property:</strong>
        <p>Go to your Google Analytics account. If you already have a Google Analytics 4 (GA4) property, select it. If you don’t have one, continue to the next step to create a new GA4 property.</p>
    </li>
    <li>
        <strong>Go to the Admin section:</strong>
        <p>In the bottom left corner of the screen, click the gear icon to access the Admin settings.</p>
    </li>
    <li>
        <strong>Create a new property if necessary:</strong>
        <p>In the top left, click the **+ Create Property** button. Follow the steps to set up your new property, making sure to select **Other** as the business objective during the setup.</p>
    </li>
    <li>
        <strong>Note down the Measurement ID:</strong>
        <p>Once your property is created, you'll find the **Measurement ID** starting with "G-". This unique identifier will allow you to track data from your website. Copy the Measurement ID as you will need it for the plugin settings.</p>
        <p><strong>Example:</strong> <code>G-XXXXXXXXXX</code></p>
    </li>
    <li>
        <strong>Create a Measurement Protocol API Secret:</strong>
        <p>To send data from your website to GA4, you need to create an API secret. Under your web data stream settings, find the "Measurement Protocol API secrets" section and click "Create". Give your API secret a descriptive name (e.g., "GA IP2C Plugin Secret") and click "Create". Copy the API Secret as you will need it in the plugin settings.</p>
    </li>
    <li>
        <strong>Enter the Measurement ID and API Secret in the plugin's settings:</strong>
        <p>Now that you have your Measurement ID and API Secret, go to your WordPress admin dashboard, navigate to <strong>Settings</strong> -> <strong>GA IP2C Settings</strong>, and enter these details into the appropriate fields. This will link your WordPress site to Google Analytics 4 for event tracking.</p>
    </li>
</ol>

<a href="<?php echo esc_url(plugins_url('GA_1.png', __FILE__)); ?>" target="_blank">
    <img src="<?php echo esc_url(plugins_url('GA_1.png', __FILE__)); ?>" alt="Create Property" style="width:100%; max-width:600px;">
</a>

<a href="<?php echo esc_url(plugins_url('GA_2.png', __FILE__)); ?>" target="_blank">
    <img src="<?php echo esc_url(plugins_url('GA_2.png', __FILE__)); ?>" alt="Measurement ID в GA4" style="width:100%; max-width:600px;">
</a>

<a href="<?php echo esc_url(plugins_url('GA_3.png', __FILE__)); ?>" target="_blank">
    <img src="<?php echo esc_url(plugins_url('GA_3.png', __FILE__)); ?>" alt="Measurement Protocol API Secret" style="width:100%; max-width:600px;">
</a>

<h3>2. Configuring GA4 to Accept Custom Events</h3>
<ol>
    <li>Open your Google Analytics 4 property and go to the <strong>Events</strong> section.</li>
    <li>Verify if the events sent by the plugin (e.g., `page_view`, `button_click`, `form_submission`) are being received.</li>
    <li>Optionally, create custom events for more specific tracking via <strong>Admin -> Events -> Create Event</strong>.</li>
    <li>
        To create custom dimensions for the following **user properties**, navigate to <strong>Admin -> Custom Definitions -> Custom Dimensions</strong> in your GA4 property:

        <ol>
            <li>Click on <strong>Create Custom Dimension</strong>.</li>
            <li>In the <strong>Dimension name</strong> field, enter the name of the parameter (e.g., <em>Company Name</em>).</li>
            <li>Set the <strong>Scope</strong> to <strong>User</strong>.</li>
            <li>In the <strong>User property</strong> field, enter the parameter name exactly as listed below (e.g., <code>company_name</code>).</li>
            <li>Click <strong>Save</strong>.</li>
            <li>Repeat steps 1–5 for each of the following user properties:</li>
        </ol>

        <ul>
            <li><strong>company_name</strong>: The company name derived from the visitor's IP address.</li>
            <li><strong>company_city</strong>: The city where the company is located.</li>
            <li><strong>company_country_code</strong>: The country code of the company.</li>
            <li><strong>company_industry</strong>: The industry in which the company operates.</li>
            <li><strong>company_industry_code</strong>: The industry code of the company.</li>
            <li><strong>company_revenue</strong>: The revenue of the company.</li>
            <li><strong>company_revenue_class</strong>: The revenue class of the company (e.g., small, medium, large).</li>
            <li><strong>company_employee_size</strong>: The number of employees in the company.</li>
            <li><strong>company_employee_class</strong>: The employee size class (e.g., small, medium, large).</li>
            <li><strong>company_zip</strong>: The postal ZIP code of the company.</li>
            <li><strong>company_region</strong>: The region where the company is located.</li>
        </ul>
    </li>
    <li>Monitor events in real time via <strong>Realtime</strong> or <strong>DebugView</strong> in GA4.</li>
</ol>

<a href="<?php echo esc_url(plugins_url('GA_4.png', __FILE__)); ?>" target="_blank">
    <img src="<?php echo esc_url(plugins_url('GA_4.png', __FILE__)); ?>" alt="Events GA4" style="width:100%; max-width:600px;">
</a>

<h3>3. Creating Custom Reports in Google Analytics 4</h3>
<p>Follow these steps to create custom reports based on the data collected by the plugin:</p>
<ol>
    <li>Open your Google Analytics 4 property and navigate to <strong>Explore</strong> (or <strong>Analysis Hub</strong>).</li>
    <li>Create a new exploration and add custom dimensions such as <strong>company_name</strong>, <strong>utm_source</strong>, etc.</li>
    <li>Drag and drop dimensions and metrics to build your report, and use segments and filters to refine the data.</li>
    <li>Save and share your custom reports for further analysis.</li>
</ol>

<h2>Frequently Asked Questions</h2>
<h4>How do I obtain the IP2C API Token?</h4>
<p>You can obtain the IP2C API Token by registering on the Wiredminds website at <a href="https://wiredminds.de" target="_blank">https://wiredminds.de</a>.</p>

<h4>Where do I find my Google Analytics ID and API Secret?</h4>
<p>Your Google Analytics ID and API Secret are available in the Google Analytics 4 interface under the Admin section, specifically in the Data Streams settings.</p>

<h4>What data does this plugin track?</h4>
<p>The plugin tracks page views, button clicks, form submissions, outbound links, file downloads, scroll depth, video interactions, and more. It also retrieves and includes company data based on the visitor's IP address.</p>

<h4>How secure is the data being tracked and sent to GA4?</h4>
<p>The plugin uses standard WordPress best practices for sanitization and data transmission, ensuring that the data is securely tracked and sent to Google Analytics 4.</p>

<h4>How do I track the performance of my marketing campaigns?</h4>
<p>Ensure that your marketing URLs include UTM parameters. The plugin will automatically capture and send these parameters to GA4, allowing you to track the performance of your campaigns directly in Google Analytics.</p>
</div>
