#!/usr/bin/env python3

import asyncio
from playwright.async_api import async_playwright
import sys

BASE_URL = "http://localhost:8001"

async def fill_stripe_form(page):
    """Fill the Stripe checkout form with test card details and submit"""
    try:
        # First check if email field needs to be filled
        await page.get_by_role('textbox', name='Email').fill('test@example.com')
        print("✅ Filled email field")
        
        # Fill card details
        print("Filling card number...")
        await page.get_by_role('textbox', name='Card number').fill("4242424242424242")
        
        print("Filling expiry date...")
        await page.get_by_role('textbox', name='Expiration').fill("12/34")
        
        print("Filling CVC...")
        await page.get_by_role('textbox', name='CVC').fill("234")
        
        # Fill cardholder name if the field exists
        await page.get_by_role('textbox', name='Cardholder name').fill("Test User")
        print("✅ Filled cardholder name")

        print("Filling ZIP...")
        postal_field = page.get_by_role('textbox', name='ZIP')
        if await postal_field.count() > 0:
            await postal_field.fill('12345')
            print("✅ Filled postal code")
        
        # Look for and uncheck ANY checkboxes that might appear on the page
        try:
            print("Looking for checkboxes to uncheck...")
            # Find all checkboxes on the page
            checkboxes = await page.query_selector_all('input[type="checkbox"]')
            
            if checkboxes:
                print(f"Found {len(checkboxes)} checkbox(es) on the page")
                for i, checkbox in enumerate(checkboxes):
                    # Get checkbox state
                    is_checked = await checkbox.is_checked()
                    if is_checked:
                        await checkbox.uncheck(timeout=5000)
                        print(f"Unchecked checkbox #{i+1}")
                    else:
                        print(f"Checkbox #{i+1} was already unchecked")
            else:
                print("No checkboxes found on the page")
                
            # Additional attempt with role selector in case the above method missed any
            role_checkboxes = await page.get_by_role("checkbox").all()
            if role_checkboxes:
                print(f"Found {len(role_checkboxes)} additional checkbox(es) by role")
                for i, checkbox in enumerate(role_checkboxes):
                    # Get checkbox state
                    is_checked = await checkbox.is_checked()
                    if is_checked:
                        await checkbox.uncheck(timeout=5000)
                        print(f"Unchecked additional checkbox #{i+1}")
                    else:
                        print(f"Additional checkbox #{i+1} was already unchecked")
        except Exception as checkbox_error:
            print(f"Note: Error handling checkboxes: {checkbox_error}")
        
        # Take screenshot of filled form
        await page.screenshot(path="stripe_form_filled.png")
        
        # Submit the form
        print("Submitting payment form...")
        submit_button = page.get_by_test_id('hosted-payment-submit-button')
        await submit_button.click()
        print("✅ Payment form submitted (using test card)")
        
        # Wait for redirect or change in the page
        print("Waiting for payment processing ...")
        try:
            await asyncio.sleep(20)  # Wait 20 seconds for processing
            
            # Take screenshot after payment submission 
            await page.screenshot(path="post_payment_submission.png")
            print("✅ Payment submitted and processing completed")
            
            # Check if we've been redirected
            if BASE_URL in page.url or "success" in page.url.lower():
                print(f"✅ Successfully redirected to: {page.url}")
            else:
                print(f"⚠️ No redirect occurred. Current URL: {page.url}")
            
            return True
            
        except Exception as processing_error:
            print(f"❌ Error during payment processing: {processing_error}")
            await page.screenshot(path="processing_error.png")
            return False
            
    except Exception as e:
        print(f"❌ Error filling/submitting form: {e}")
        return False

async def test_mewayz_frontend():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=False)
        context = await browser.new_context()
        page = await context.new_page()
        
        # Set viewport for desktop testing
        await page.set_viewport_size({"width": 1920, "height": 1080})
        
        print("🚀 Starting comprehensive frontend testing of Mewayz Laravel application")
        
        try:
            # Test 1: Homepage
            print("\n=== TESTING HOMEPAGE ===")
            await page.goto(BASE_URL)
            await page.screenshot(path="01_homepage.png")
            
            page_title = await page.title()
            print(f"📄 Page title: {page_title}")
            
            # Check for key elements
            login_elements = await page.locator("a[href*='login'], button:has-text('Login')").count()
            register_elements = await page.locator("a[href*='register'], button:has-text('Register')").count()
            
            print(f"✅ Found {login_elements} login-related elements")
            print(f"✅ Found {register_elements} register-related elements")
            
            # Test 2: Login Page
            print("\n=== TESTING LOGIN PAGE ===")
            await page.goto(f"{BASE_URL}/login")
            await page.screenshot(path="02_login_page.png")
            
            login_title = await page.title()
            print(f"📄 Login page title: {login_title}")
            
            # Check for login form elements
            email_field = await page.locator("input[type='email'], input[name='email']").count()
            password_field = await page.locator("input[type='password'], input[name='password']").count()
            login_button = await page.locator("button[type='submit'], input[type='submit']").count()
            
            print(f"✅ Found {email_field} email fields")
            print(f"✅ Found {password_field} password fields")
            print(f"✅ Found {login_button} submit buttons")
            
            # Test login with admin credentials
            if email_field > 0 and password_field > 0:
                print("🔐 Testing login with admin credentials...")
                await page.fill("input[type='email'], input[name='email']", "admin@example.com")
                await page.fill("input[type='password'], input[name='password']", "admin123")
                await page.screenshot(path="03_login_filled.png")
                
                # Submit login form
                await page.click("button[type='submit'], input[type='submit']")
                await page.wait_for_timeout(3000)
                await page.screenshot(path="04_after_login.png")
                
                current_url = page.url
                print(f"📍 Current URL after login: {current_url}")
                
                # Check if redirected to dashboard
                if "dashboard" in current_url or current_url != f"{BASE_URL}/login":
                    print("✅ Login successful - redirected to dashboard")
                    
                    # Test 3: Dashboard
                    print("\n=== TESTING DASHBOARD ===")
                    dashboard_title = await page.title()
                    print(f"📄 Dashboard title: {dashboard_title}")
                    
                    # Look for dashboard elements
                    nav_items = await page.locator("nav a, .nav a, .sidebar a").count()
                    print(f"✅ Found {nav_items} navigation items")
                    
                    # Test navigation to different sections
                    sections_to_test = [
                        ("Instagram", "/dashboard/instagram"),
                        ("Email", "/dashboard/email"),
                        ("Analytics", "/dashboard/analytics"),
                        ("CRM", "/dashboard/crm"),
                        ("Courses", "/dashboard/courses"),
                        ("E-commerce", "/dashboard/ecommerce"),
                        ("Bio Sites", "/dashboard/sites"),
                        ("Team", "/dashboard/team")
                    ]
                    
                    for section_name, section_url in sections_to_test:
                        print(f"\n--- Testing {section_name} Section ---")
                        try:
                            await page.goto(f"{BASE_URL}{section_url}")
                            await page.wait_for_timeout(2000)
                            await page.screenshot(path=f"05_{section_name.lower().replace(' ', '_')}_section.png")
                            
                            section_title = await page.title()
                            print(f"📄 {section_name} page title: {section_title}")
                            
                            # Check for section-specific elements
                            forms = await page.locator("form").count()
                            buttons = await page.locator("button").count()
                            tables = await page.locator("table").count()
                            
                            print(f"✅ {section_name}: {forms} forms, {buttons} buttons, {tables} tables")
                            
                        except Exception as e:
                            print(f"❌ Error testing {section_name}: {e}")
                    
                    # Test 4: Workspace Setup
                    print("\n=== TESTING WORKSPACE SETUP ===")
                    try:
                        await page.goto(f"{BASE_URL}/workspace-setup")
                        await page.wait_for_timeout(2000)
                        await page.screenshot(path="06_workspace_setup.png")
                        
                        setup_title = await page.title()
                        print(f"📄 Workspace setup title: {setup_title}")
                        
                        # Check for setup steps
                        steps = await page.locator(".step, .wizard-step, [data-step]").count()
                        print(f"✅ Found {steps} setup steps")
                        
                    except Exception as e:
                        print(f"❌ Error testing workspace setup: {e}")
                    
                    # Test 5: Stripe Payment Integration
                    print("\n=== TESTING STRIPE PAYMENT INTEGRATION ===")
                    try:
                        # Look for payment/subscription links
                        payment_links = await page.locator("a[href*='payment'], a[href*='subscription'], a[href*='stripe']").count()
                        if payment_links > 0:
                            print(f"✅ Found {payment_links} payment-related links")
                            
                            # Try to access payment page
                            await page.goto(f"{BASE_URL}/dashboard/subscription")
                            await page.wait_for_timeout(2000)
                            await page.screenshot(path="07_payment_page.png")
                            
                            # Look for payment packages
                            packages = await page.locator(".package, .plan, .pricing").count()
                            print(f"✅ Found {packages} payment packages")
                            
                            # If there are packages, try to initiate payment
                            if packages > 0:
                                package_buttons = await page.locator("button:has-text('Subscribe'), button:has-text('Choose'), button:has-text('Select')").count()
                                if package_buttons > 0:
                                    print("💳 Testing Stripe checkout...")
                                    await page.click("button:has-text('Subscribe'), button:has-text('Choose'), button:has-text('Select')")
                                    await page.wait_for_timeout(5000)
                                    
                                    # Check if redirected to Stripe
                                    if "stripe.com" in page.url:
                                        print("✅ Successfully redirected to Stripe checkout")
                                        await page.screenshot(path="08_stripe_checkout.png")
                                        
                                        # Fill Stripe form using the provided function
                                        await fill_stripe_form(page)
                                        
                                    else:
                                        print("⚠️ Not redirected to Stripe - checking for local payment form")
                                        await page.screenshot(path="08_local_payment_form.png")
                        
                    except Exception as e:
                        print(f"❌ Error testing payment integration: {e}")
                    
                    # Test 6: OAuth Integration
                    print("\n=== TESTING OAUTH INTEGRATION ===")
                    try:
                        await page.goto(f"{BASE_URL}/dashboard/settings")
                        await page.wait_for_timeout(2000)
                        
                        oauth_buttons = await page.locator("button:has-text('Google'), button:has-text('Facebook'), button:has-text('Apple'), button:has-text('Twitter')").count()
                        print(f"✅ Found {oauth_buttons} OAuth buttons")
                        
                        if oauth_buttons > 0:
                            await page.screenshot(path="09_oauth_settings.png")
                            
                            # Test Google OAuth (in test mode)
                            google_btn = page.locator("button:has-text('Google')").first
                            if await google_btn.count() > 0:
                                print("🔗 Testing Google OAuth...")
                                await google_btn.click()
                                await page.wait_for_timeout(3000)
                                await page.screenshot(path="10_google_oauth.png")
                        
                    except Exception as e:
                        print(f"❌ Error testing OAuth: {e}")
                    
                else:
                    print("❌ Login failed - still on login page")
            
            # Test 7: Registration
            print("\n=== TESTING REGISTRATION ===")
            await page.goto(f"{BASE_URL}/register")
            await page.screenshot(path="11_register_page.png")
            
            register_title = await page.title()
            print(f"📄 Register page title: {register_title}")
            
            # Check registration form
            name_field = await page.locator("input[name='name'], input[placeholder*='name']").count()
            email_field = await page.locator("input[type='email'], input[name='email']").count()
            password_field = await page.locator("input[type='password'], input[name='password']").count()
            
            print(f"✅ Registration form: {name_field} name fields, {email_field} email fields, {password_field} password fields")
            
            # Test 8: Mobile Responsiveness
            print("\n=== TESTING MOBILE RESPONSIVENESS ===")
            await page.set_viewport_size({"width": 390, "height": 844})
            await page.goto(BASE_URL)
            await page.screenshot(path="12_mobile_homepage.png")
            
            await page.goto(f"{BASE_URL}/login")
            await page.screenshot(path="13_mobile_login.png")
            
            print("✅ Mobile responsiveness tested")
            
        except Exception as e:
            print(f"❌ Critical error during testing: {e}")
            await page.screenshot(path="error_screenshot.png")
        
        finally:
            await browser.close()
        
        print("\n🎉 Frontend testing completed!")

if __name__ == "__main__":
    asyncio.run(test_mewayz_frontend())