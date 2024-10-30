=== IP2GA ===
Contributors: wiredmindshelp  
Tags: B2B Google Analytics, GA4 Leads, B2B Tracking, Company Data, Leads Analytics
Donate link: https://wiredminds.de  
Requires at least: 4.8.1  
Tested up to: 6.6  
Requires PHP: 7.4  
Stable tag: 1.6.3  
License: GPLv2 or later  
License URI: https://www.gnu.org/licenses/gpl-2.0.html  

Track all user activities on the site, including page views, button clicks, and form submissions, and send them to Google Analytics 4.

== Description ==

The IP2GA plugin is designed to capture and send comprehensive user interaction data to Google Analytics 4 (GA4). It automatically tracks various user activities on your site, such as page views, clicks, form submissions, and more. Additionally, it retrieves company data based on the visitor's IP address using the IP2C API and integrates this information into your GA4 events, allowing for more detailed and customized tracking.

The IP2GA plugin requires the services of third-party providers to ensure full functionality. These third-party services are used for data processing and transmission to capture and analyze business data and usage information. The following third-party services are used in the plugin:
 
### 1. Google Analytics
The plugin uses Google Analytics to collect and process usage data for analysis purposes.
- **Privacy Policy:** [Google Privacy Policy](https://policies.google.com/privacy)
- **Terms of Service:** [Google Analytics Terms of Service](https://marketingplatform.google.com/about/analytics/terms/us/)
 
### 2. RapidAPI
The plugin uses RapidAPI to facilitate communication between the plugin and external APIs. Data, such as IP addresses, is forwarded to IP2Company.
- **Privacy Policy:** [RapidAPI Privacy Policy](https://rapidapi.com/privacy)
- **Terms of Service:** [RapidAPI Terms of Service](https://rapidapi.com/terms)
 
### 3. YouTube API
- **YouTube API**: Tracks interactions with embedded YouTube videos via the YouTube IFrame API. [Terms of Service](https://www.youtube.com/t/terms)

### 4. IP2Company (WiredMinds)
The plugin uses IP2Company, a service provided by WiredMinds, to identify business data based on visitors' IP addresses.
- **Privacy Policy:** [WiredMinds Privacy Policy](https://www.wiredminds.de/datenschutz/)
- **Terms of Service:** [WiredMinds Terms of Service](https://www.wiredminds.de/agb/)
 

**Features:**

* Tracks and sends various user interactions (page views, button clicks, form submissions, etc.) to Google Analytics 4.
* Retrieves company data based on the visitor's IP address and includes it in GA4 events.
* Supports tracking for outbound links, file downloads, scroll depth, video interactions, and more.
* Automatically handles different traffic sources and user agents for accurate reporting, including UTM parameters for campaign tracking.
* Provides a settings page to configure the IP2C API Token, GA4 Tracking ID, and GA4 API Secret.

== Installation ==

1. Upload the `ip2ga` folder to the `/wp-content/plugins/` directory. Or upload the archive via the plugins control panel.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to 'Settings' -> 'IP2GA Settings' to configure your IP2C API Token, Google Analytics ID, and GA4 API Secret.

== Setting Up Google Analytics 4 ==

To ensure the plugin fully integrates with Google Analytics 4, follow these steps:

### 1. Create a Measurement ID and API Secret in GA4

1. Open your Google Analytics 4 property.
2. Go to the **Admin** section (gear icon at the bottom left).
3. Under the Account column, select your account or create a new one if you don't have it.
4. In the top left, click + **Create Property** or use existing Property .
5. Follow the steps to set up your  **only for new property**. Make sure to select **All** as the business objective during the setup.
6. Note down the **Measurement ID** (it starts with "G-").
7. Click on **Measurement Protocol API secrets** and create a new secret. Name it, and then copy the **API Secret**.
8. Enter the Measurement ID and API Secret in the plugin's settings page in WordPress.

### 2. Configuring GA4 to Accept Custom Events

1. **Open your Google Analytics 4 property.**

   - Go to your Google Analytics account and select your GA4 property.

2. **Navigate to the Events section.**

   - On the left-hand side, select **Events** to view the events being sent to your property.

3. **Verify Existing Events:**

   - Check if the events sent by the plugin (e.g., `page_view`, `button_click`, `form_submission`, `video_play`, `scroll_depth`, `download`) are being received.

4. **Create Custom Events (Optional):**

   - If you wish to create custom events based on the plugin's events for more specific tracking:
     - Go to **Admin** -> **Events** -> **Create Event**.
     - Click **Create** and provide a name for the new event.
     - In the **Matching conditions** field, set the conditions under which this event will be created (e.g., when an event contains specific parameters).

5. **Set Up Custom Dimensions to Capture Additional Parameters:**

   The plugin sends the following additional parameters with each event, which can be used to create custom dimensions:
   
   - **company_name**: The company name derived from the IP data.
   - **company_city**: The city where the company is located.
   - **company_country_code**: The country code of the company.
   - **company_industry**: The industry in which the company operates.
   - **company_industry_code**: The industry code of the company.
   - **company_revenue**: The revenue of the company.
   - **company_revenue_class**: The revenue class of the company.
   - **company_employee_size**: The size of the company based on the number of employees.
   - **company_employee_class**: The employee size class of the company.
   - **company_zip**: The ZIP code of the company.
   - **company_region**: The region where the company is located.

   To create custom dimensions:

   1. Go to **Admin** -> **Custom Definitions** -> **Custom Dimensions**.
   2. Click **Create Custom Dimension**.
   3. In the **Dimension name** field, enter a descriptive name (e.g., "Company Name" for the `company_name` parameter).
   4. In the **Scope** field, select **User**.
   5. In the **Event Parameter** field, enter the parameter name sent by the plugin (e.g., `company_name`).
   6. Click **Save** to create the dimension.

6. **Monitoring Events:**

   - Use **Realtime** reports in GA4 to monitor incoming events as they occur.
   - For detailed analysis, use **DebugView**:
     - On the left-hand side, go to **Configure** -> **DebugView**.
     - Here, you can see incoming events and their associated parameters, helping you to debug and refine your tracking setup.

### 3. Creating Custom Reports in Google Analytics 4

To fully leverage the data about companies and user behavior collected by the IP2GA plugin, you can create custom reports in Google Analytics 4. These reports will allow you to analyze the performance of different companies, industries, or campaigns, and understand how user behavior on your site correlates with company data.

#### Step-by-Step Guide to Creating a Custom Report:

1. **Open your Google Analytics 4 property.**

   - Go to your Google Analytics account and select your GA4 property.

2. **Navigate to the Analysis Hub.**

   - On the left-hand menu, click on **Explore** (or **Analysis Hub**), which allows you to create custom explorations and reports.

3. **Create a New Exploration:**

   - Click on **Blank** to start with a blank exploration template.

4. **Add Dimensions and Metrics:**

   - In the **Variables** pane, click **+** next to **Dimensions** and select the custom dimensions you created (e.g., `company_name`, `company_city`, `company_industry`, `utm_source`, etc.).
   - Click **+** next to **Metrics** and add relevant metrics like `Total Users`, `Conversions`, `Revenue`, etc.

5. **Building the Report:**

   - Drag and drop dimensions like `company_name`, `company_industry`, or `utm_source` into the **Rows** section.
   - Drag relevant metrics into the **Values** section to see the performance of each company or campaign.
   - You can also use filters to narrow down your analysis. For example, you could filter by `company_country_code` to see only users from a specific country.

6. **Adding Segments:**

   - Use segments to break down the data further. For example, create segments based on user behavior, such as users who completed a form submission or downloaded a file.
   - To create a segment, click on **Segments** in the **Variables** pane and define the conditions for your segment.

7. **Visualizing the Data:**

   - You can switch between different visualization types like tables, line charts, or bar charts to better understand the data.
   - Experiment with different views to find the most insightful presentation of the data.

8. **Saving and Sharing the Report:**

   - Once you're satisfied with the report, click **Save** to store it for future reference.
   - You can also share the report with others in your organization by clicking on **Share**.

#### Example Use Cases for Custom Reports:

- **Company Performance Analysis:**
  - Track how different companies interact with your site and how these interactions correlate with conversions or revenue.
  
- **Industry Trends:**
  - Analyze how companies from different industries behave on your site, which can inform your marketing or content strategies.

- **Campaign Effectiveness:**
  - Use UTM parameters to evaluate the performance of different marketing campaigns, comparing them by traffic source, medium, or campaign name.

#### Benefits of Custom Reports:

- **Tailored Insights:** Get specific insights that matter most to your business, beyond the standard reports offered by GA4.
- **Actionable Data:** Use the detailed company and behavior data to make informed decisions about targeting, marketing strategies, and user engagement on your site.
- **Enhanced Segmentation:** Break down your audience into meaningful segments for deeper analysis.

By setting up these custom reports, you can unlock the full potential of the data collected by the GA IP2C Full Site and Event Tracking for GA4 plugin, providing valuable insights into how different companies and user behaviors impact your business outcomes.


### 4. Monitoring Events in GA4

1. Use **Realtime** reports to monitor incoming events as they happen.
2. Access **DebugView** under **Configure** -> **DebugView** in GA4 to see detailed event flow and parameter values.
3. To view campaign performance, use **Acquisition** reports to analyze traffic sources and UTM parameters.

== Frequently Asked Questions ==

= How do I obtain the IP2C API Token? =

You can obtain the IP2C API Token by registering on the Wiredminds website at [https://wiredminds.de](https://wiredminds.de).

= Where do I find my Google Analytics ID and API Secret? =

Your Google Analytics ID and API Secret are available in the Google Analytics 4 interface under the Admin section, specifically in the Data Streams settings.

= What data does this plugin track? =

The plugin tracks page views, button clicks, form submissions, outbound links, file downloads, scroll depth, video interactions, and more. It also retrieves and includes company data based on the visitor's IP address.

= How secure is the data being tracked and sent to GA4? =

The plugin uses standard WordPress best practices for sanitization and data transmission, ensuring that the data is securely tracked and sent to Google Analytics 4.

= How do I track the performance of my marketing campaigns? =

Ensure that your marketing URLs include UTM parameters. The plugin will automatically capture and send these parameters to GA4, allowing you to track the performance of your campaigns directly in Google Analytics.

= I detected a provider / ISP - how can I hide them? =

Here is a link where you can report providers, which will be hidden from our side. :[Tracking – Providercheck](https://help.wiredminds.de/tracking-providercheck/).

== Screenshots ==

1. Settings Page - Configure your IP2C API Token or RapidAPI Token, Google Analytics ID, and GA4 API Secret.

== Changelog ==

= 1.6.3 =
* Updated AJAX tracking for form submit.
* Updated visitor ID generation method

= 1.6.1 =
* Updated AJAX tracking for link click event.
* Added company name check before sending to GA4.

= 1.6 =
* Added consent banner.
* Updated settings page for GA4 tracking script enable/disabled.
* Updating documentation.

= 1.5 =
* Updating documentation, improving usability.

= 1.4 =
* Updated settings page for AJAX tracking enable/disabled.

= 1.3 =
* Added Rapidapi integration https://rapidapi.com/wiredminds-gmbh-wiredminds-gmbh-default/api/ip2company3 .
* Updated settings page for configuring RapidAPI Token.

= 1.2 =
* Added event tracking for videos, scroll depth, and file downloads.
* Improved handling of various traffic sources and user agents.
* Added support for WordPress 6.2.
* Updated documentation and improved error handling.

= 1.1 =
* Introduced settings page for configuring IP2C API Token and GA4 details.
* Initial release of the plugin with basic tracking and IP2C integration.

== Upgrade Notice ==

= 1.2 =
Ensure that your site is running WordPress 4.8.1 or higher before upgrading.

== License ==

This plugin is licensed under the GPLv2 or later. For more information, see [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html).
