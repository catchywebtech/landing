<?php
// This file is included by index.php
// It contains the HTML for the contact form fields.
?>
<div id="form-result" class="mb-4 text-center"></div>

<div class="mb-6">
    <label for="name" class="sr-only">Name</label>
    <input type="text" id="name" name="name" placeholder="Your Name" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 ring-primary" required>
</div>

<div class="mb-6">
    <label for="service" class="sr-only">Select a Service</label>
    <select id="service" name="service" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 ring-primary bg-white" required>
        <option value="" disabled selected>Select a Service</option>
        <?php
            // The $pdo object is available from the parent index.php file
            $services_stmt_form = $pdo->query("SELECT title FROM services ORDER BY title");
            foreach ($services_stmt_form->fetchAll(PDO::FETCH_COLUMN) as $service_title) {
                echo "<option value='" . htmlspecialchars($service_title) . "'>" . htmlspecialchars($service_title) . "</option>";
            }
        ?>
    </select>
</div>

<div class="mb-6">
    <label for="contact_number" class="sr-only">Contact Number</label>
    <input type="tel" id="contact_number" name="contact_number" placeholder="Contact Number" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 ring-primary" required>
</div>
<div class="mb-6">
    <label for="whatsapp_number" class="sr-only">WhatsApp Number</label>
    <input type="tel" id="whatsapp_number" name="whatsapp_number" placeholder="WhatsApp Number" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 ring-primary" required>
</div>
<div class="mb-6 flex items-center">
    <input type="checkbox" id="same_as_contact" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
    <label for="same_as_contact" class="ml-2 block text-sm text-gray-700">WhatsApp number is same as Contact number</label>
</div>
<div class="mb-6">
    <label for="location" class="sr-only">Your Location (Optional)</label>
    <input type="text" id="location" name="location" placeholder="Your Location (Optional)" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 ring-primary">
</div>
<div class="text-center">
    <button type="submit" id="submit-button" class="bg-primary text-white font-bold py-3 px-8 rounded-full hover:bg-primary-dark w-full md:w-auto">Submit</button>
</div>
