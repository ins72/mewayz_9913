
<x-layouts.site>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mewayz Partnership Opportunity - Vetting Form</title>
    <!-- Importing Inter for general text and Staatliches for a bold, industrial header feel -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Staatliches&display=swap" rel="stylesheet">
    <style>
        /* General Reset & Base Styles */
        :root {
            --bg-dark: #000000; /* Pure black */
            --bg-medium: #0A0A0A; /* Very dark grey */
            --bg-light: #1A1A1A; /* Dark grey */
            --text-color: #E0E0E0; /* Softer white text */
            --primary-red: #B30000; /* Deeper, more muted red */
            --primary-red-light: #CC0000; /* Slightly brighter for accents, but still deep */
            --primary-red-dark: #800000; /* Even deeper red, almost maroon */
            --border-color-dark: #2A2A2A; /* More subtle border, less contrast */
            --shadow-dark: rgba(0, 0, 0, 0.5); /* Less opaque shadow */
            --shadow-light: rgba(0, 0, 0, 0.3); /* Lighter shadow */
            --transition-speed: 0.3s; /* Slightly faster transitions for crispness */
        }
      ::selection {
  color: red;
  background: black;
      }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--bg-medium) 100%);
            color: var(--text-color);
            font-family: 'Inter', sans-serif; /* Inter for general text */
            min-height: 100vh;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px; /* Increased overall padding */
            scroll-behavior: smooth;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* Container for the form */
        .container {
            max-width: 850px; /* Slightly wider container */
            width: 100%;
            margin: 0 auto;
            padding: 40px; /* Enhanced padding */
            background: var(--bg-medium);
            border-radius: 25px; /* More rounded */
            box-shadow: 0 20px 50px var(--shadow-dark); /* Deeper, more spread shadow */
            position: relative;
            border: 1px solid var(--border-color-dark); /* Subtle container border */
        }

        /* Header Styling */
        .header {
            text-align: center;
            margin-bottom: 60px; /* More space below header */
            padding: 40px 0;
            border-bottom: 3px solid var(--primary-red); /* Thicker border */
            position: relative; /* For pseudo-elements */
        }

        .header::after {
            content: '';
            position: absolute;
            left: 50%;
            bottom: -1.5px; /* Align with border-bottom */
            transform: translateX(-50%);
            width: 80px; /* Short line below the main border */
            height: 3px;
            background: var(--primary-red-light);
            /* Softened glow effect - more of a highlight now */
            box-shadow: 0 0 5px rgba(204, 0, 0, 0.4); 
            border-radius: 5px;
        }

        .header h1 {
            font-size: 3.5em; /* Larger heading */
            color: var(--primary-red);
            margin-bottom: 18px;
            /* Using 'Airstrike' (requires custom font import) as primary, Staatliches as fallback */
            font-family: 'Airstrike', 'Staatliches', sans-serif;
            letter-spacing: 2px;
            font-weight: 700;
            /* Removed text shadow/glow */
            text-shadow: none; 
        }

        .header p {
            font-size: 1.6em; /* Larger subheading */
            color: #b0b0b0; /* Softer grey */
            font-weight: 300;
        }

        /* Language Selector */
        .language-selector {
            text-align: center;
            margin-bottom: 50px; /* More space */
        }

        .language-selector select {
            background: var(--bg-light);
            color: var(--text-color);
            border: 2px solid var(--primary-red);
            padding: 12px 20px; /* Reduced padding for more auto height flexibility */
            border-radius: 15px; /* More rounded */
            font-size: 20px;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23e0e0e0" height="28" viewBox="0 0 24 24" width="28" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/><path d="M0 0h24v24H0z" fill="none"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 24px;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.3);
            min-width: 200px; /* Ensure minimum width */
            height: auto; /* Ensure auto height for select/dropdown */
        }

        .language-selector select:hover {
            background: #222222; /* Slightly darker on hover */
            border-color: var(--primary-red-light);
            transform: translateY(-3px); /* More pronounced lift */
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
        }
        .language-selector select:focus {
            outline: none;
            border-color: var(--primary-red-light);
            /* Softened glow effect on focus */
            box-shadow: 0 0 8px rgba(204, 0, 0, 0.5);
        }

        /* Company Introduction Section */
        .company-intro {
            background: var(--bg-medium);
            padding: 40px;
            margin-bottom: 50px;
            border-radius: 20px;
            border: 1px solid var(--border-color-dark);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
        }

        .company-intro h2 {
            font-size: 2.8em;
            color: var(--primary-red-light);
            margin-bottom: 15px;
            letter-spacing: 1.5px;
            font-weight: 700;
        }

        .company-intro p {
            font-size: 1.2em;
            color: #c0c0c0;
            line-height: 1.8;
            max-width: 700px;
            margin-bottom: 15px;
        }

        .company-intro .company-logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--primary-red);
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 3em;
            color: white;
            font-family: 'Staatliches', sans-serif;
            margin-bottom: 10px;
            box-shadow: 0 0 15px rgba(179, 0, 0, 0.7);
            overflow: hidden; /* Ensure content stays within circle */
        }
        .company-intro .company-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%; /* Make image fit the circle */
        }


        /* Info Box */
        .info-box {
            background: var(--bg-light);
            padding: 25px 35px;
            margin-bottom: 50px;
            border-radius: 20px;
            border: 1px solid var(--border-color-dark);
            box-shadow: 0 10px 25px var(--shadow-light);
            text-align: center;
            font-size: 1.2em; /* Slightly larger text */
            line-height: 1.8;
            position: relative;
            overflow: hidden;
        }

        .info-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, transparent, var(--primary-red-light), transparent);
        }

        .info-box p {
            margin: 0;
            color: #d0d0d0;
        }

        .info-box a {
            color: var(--primary-red-light);
            font-weight: bold;
            text-decoration: none;
            transition: color var(--transition-speed) ease, text-shadow var(--transition-speed) ease;
        }

        .info-box a:hover {
            color: #ffaaaa;
            text-decoration: underline;
            text-shadow: 0 0 6px rgba(255, 170, 170, 0.4);
        }

        /* Warning Box Styling (PROFESSIONAL REDESIGN) */
        .warning-box {
            background: var(--bg-light); /* More uniform dark background */
            border: 1px solid var(--primary-red-dark); /* Thinner, deeper red border */
            padding: 30px; /* Slightly reduced padding */
            border-radius: 15px; /* Slightly less rounded */
            margin-bottom: 40px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); /* Less intense shadow */
            position: relative;
            overflow: hidden;
            display: flex; /* Use flex for layout */
            align-items: flex-start; /* Align icon and text at top */
            gap: 20px; /* Space between icon and content */
        }

        /* Removed old ::before lightning bolt */
        .warning-box::before {
            content: none;
        }

        /* New custom warning icon */
        .warning-icon {
            flex-shrink: 0; /* Prevent shrinking */
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary-red); /* Red circle background */
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Staatliches', sans-serif; /* A strong font for the ! */
            font-size: 2em;
            color: white;
            font-weight: bold;
            box-shadow: 0 0 8px rgba(179, 0, 0, 0.5); /* Subtle glow for the icon */
        }

        .warning-box h3 {
            color: var(--primary-red-light);
            margin-bottom: 20px; /* Reduced margin */
            font-size: 1.8em; /* Slightly smaller heading */
            text-align: left; /* Align text to left */
            font-weight: 600;
            position: relative;
            z-index: 1;
            flex-grow: 1; /* Allow heading to take available space */
        }

        .warning-box ul {
            padding-left: 0; /* Remove padding from ul */
            list-style-type: none;
            position: relative;
            z-index: 1;
            flex-basis: 100%; /* Ensure ul takes full width under the header */
            margin-top: -10px; /* Pull it up slightly to align with content */
        }

        .warning-box li {
            margin-bottom: 12px; /* Reduced spacing */
            color: #cccccc;
            font-size: 1.05em; /* Slightly smaller text */
            position: relative;
            padding-left: 25px; /* Space for custom bullet */
            transition: color 0.3s ease;
        }
        .warning-box li:last-child {
            margin-bottom: 0;
        }
        .warning-box li::before {
            content: '•'; /* Simpler bullet point */
            position: absolute;
            left: 0;
            color: var(--primary-red-light);
            font-size: 1.2em;
            top: 0; /* Adjust vertical alignment */
        }
        .warning-box li:hover {
            color: #e0e0e0; /* Slight color change on hover */
        }

        /* Form Section Styling */
        .form-section {
            background: var(--bg-light);
            padding: 45px; /* Increased padding */
            margin-bottom: 50px;
            border-radius: 20px;
            border: 1px solid var(--border-color-dark);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, transparent, var(--primary-red), transparent);
            animation: sectionBorderFlow 10s infinite linear;
        }

        @keyframes sectionBorderFlow {
            0% { left: -100%; }
            50% { left: 100%; }
            100% { left: -100%; }
        }

        .form-section h2 {
            color: var(--primary-red);
            margin-bottom: 40px; /* More spacing */
            font-size: 2.3em; /* Larger heading */
            border-bottom: 2px solid #555555;
            padding-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
            /* Removed text shadow */
            text-shadow: none;
        }

        /* Form Group Styling */
        .form-group {
            margin-bottom: 40px; /* More spacing */
        }

        .form-group label {
            display: block;
            margin-bottom: 18px; /* More spacing */
            color: #c0c0c0; /* Softer label color */
            font-weight: bold;
            font-size: 1.3em; /* Larger label font */
            transition: color 0.3s ease;
        }
        .form-group label:has(+ input:focus),
        .form-group label:has(+ select:focus),
        .form-group label:has(+ textarea:focus) {
            color: var(--primary-red-light); /* Highlight label on focus */
        }


        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 16px 20px; /* Reduced padding for more auto height flexibility */
            background: var(--bg-dark);
            border: 2px solid var(--border-color-dark);
            border-radius: 15px; /* More rounded */
            color: var(--text-color);
            font-size: 20px;
            transition: border-color var(--transition-speed) ease, box-shadow var(--transition-speed) ease, background-color var(--transition-speed) ease;
            box-shadow: inset 0 4px 8px rgba(0,0,0,0.5); /* Deeper inner shadow */
            height: auto; /* Ensure auto height */
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-red-light);
            background-color: #0A0A0A; /* Slightly darker background on focus */
            /* Softened glow effect on focus */
            box-shadow: 0 0 15px rgba(204, 0, 0, 0.6), inset 0 4px 8px rgba(0,0,0,0.5); 
        }

        .form-group textarea {
            resize: vertical;
            min-height: 180px; /* Taller textareas */
        }

        /* Custom Checkbox/Radio Styling */
        .checkbox-group { 
            display: flex;
            flex-wrap: wrap;
            gap: 25px; /* Increased gap */
            margin-top: 25px;
        }

        .checkbox-item { 
            display: flex;
            align-items: center;
            background: var(--bg-dark);
            padding: 18px 28px; /* Adjusted padding */
            border-radius: 15px;
            border: 2px solid var(--border-color-dark);
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            position: relative;
            overflow: hidden;
            flex-grow: 1;
            min-width: 220px; /* Ensure a minimum width for wrapping */
        }

        .checkbox-item:hover {
            border-color: var(--primary-red-light);
            background: #202020; /* Darker hover background */
            transform: translateY(-5px); /* More pronounced lift */
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
        }
        .checkbox-item:active {
            transform: translateY(0);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }
        
        /* New: Visual feedback for the entire checkbox-item when its input is checked */
        .checkbox-item input[type="checkbox"]:checked + span {
            color: var(--primary-red-light); /* Highlight text when checked */
            font-weight: 600; /* Make text bolder when checked */
        }

        /* Specific styling for radio buttons */
        .checkbox-item input[type="radio"] { 
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 28px; /* Larger size */
            height: 28px; /* Larger size */
            background-color: #383838; /* Darker grey for unchecked state */
            border: 2px solid #707070; /* Slightly darker border */
            border-radius: 50%; /* Made round for radio */
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            margin-right: 18px; /* Increased margin */
            transition: all var(--transition-speed) ease;
        }

        .checkbox-item input[type="radio"]:checked { 
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            /* Softened glow */
            box-shadow: 0 0 5px rgba(179, 0, 0, 0.4);
        }

        .checkbox-item input[type="radio"]:checked::after { 
            content: ''; 
            width: 14px; /* Inner circle for radio */
            height: 14px; /* Inner circle for radio */
            background-color: white;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: fadeInRadio 0.3s forwards; /* New animation for radio */
        }
        @keyframes fadeInRadio { 
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        /* Specific styling for checkboxes */
        .checkbox-item input[type="checkbox"] { 
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 28px; /* Larger size */
            height: 28px; /* Larger size */
            background-color: #383838; /* Darker grey for unchecked state */
            border: 2px solid #707070; /* Slightly darker border */
            border-radius: 5px; /* Square for checkbox */
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            margin-right: 18px; /* Increased margin */
            transition: all var(--transition-speed) ease;
        }

        .checkbox-item input[type="checkbox"]:checked { 
            background-color: var(--primary-red);
            border-color: var(--primary-red-light); /* Border matches red */
            box-shadow: 0 0 8px rgba(179, 0, 0, 0.6); /* More prominent glow when checked */
        }

        .checkbox-item input[type="checkbox"]:checked::after { 
            content: '✔'; /* Checkmark for checkbox */
            font-size: 1.4em; /* Make checkmark larger */
            font-weight: bold; /* Make checkmark bolder */
            color: white; /* Ensure white color */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: fadeInCheck 0.3s forwards; /* Animation for checkbox */
            line-height: 1; /* Prevent extra space above/below checkmark */
        }
        @keyframes fadeInCheck { 
            from { opacity: 0; transform: translate(-50%, -50%) scale(0.5); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }
        
        .checkbox-item span {
            color: #d8d8d8; /* Slightly lighter text */
            font-size: 1.15em; /* Slightly larger */
            font-weight: 400;
        }

        /* Submit Button */
        .submit-btn {
            background: linear-gradient(45deg, var(--primary-red-light), var(--primary-red-dark));
            color: white;
            padding: 24px 60px; /* Even larger padding */
            border: none;
            border-radius: 15px; /* More rounded */
            font-size: 26px; /* Larger font */
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 50px; /* More space */
            text-transform: uppercase;
            letter-spacing: 2px; /* More distinct spacing */
            /* Softened glow */
            box-shadow: 0 8px 15px rgba(179, 0, 0, 0.4); 
            outline: none;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px; /* More space between text and spinner */
            position: relative; /* For bubble effect */
            overflow: hidden;
            z-index: 1;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.1); /* Less intense overlay */
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
            z-index: -1;
        }

        .submit-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .submit-btn:hover {
            background: linear-gradient(45deg, var(--primary-red-dark), #700000); /* Darker hover gradient */
            transform: translateY(-7px); /* More pronounced lift */
            /* Softened glow on hover */
            box-shadow: 0 12px 30px rgba(179, 0, 0, 0.6);
        }
        .submit-btn:active {
            transform: translateY(0);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.5);
        }

        /* Loading Spinner */
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.4); /* Softer spinner color */
            border-top: 4px solid #fff;
            border-radius: 50%;
            width: 28px; /* Larger spinner */
            height: 28px; /* Larger spinner */
            animation: spin 0.8s linear infinite; /* Faster spin */
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Results Section */
        .results {
            display: none;
            background: var(--bg-light);
            padding: 40px;
            border-radius: 20px;
            margin-top: 50px;
            border: 2px solid var(--primary-red);
            box-shadow: 0 10px 25px var(--shadow-dark);
        }

        .results h3 {
            color: var(--primary-red-light);
            margin-bottom: 25px; /* More spacing */
            font-size: 2em; /* Larger heading */
            font-weight: 600;
            text-align: center;
        }

        .results pre {
            background: var(--bg-dark);
            padding: 35px; /* More padding */
            border-radius: 15px;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-family: 'Fira Code', 'Consolas', 'Courier New', monospace; /* Fira Code would need import */
            font-size: 1.1em; /* Slightly larger font */
            line-height: 1.9; /* More line height */
            color: #f5f5f5; /* Lighter code text */
            border: 1px solid #555555; /* More visible border */
            max-height: 550px; /* Slightly more height */
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-red) var(--bg-dark);
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.4);
        }
        /* Webkit scrollbar styling */
        .results pre::-webkit-scrollbar {
            width: 10px; /* Thicker scrollbar */
        }
        .results pre::-webkit-scrollbar-track {
            background: var(--bg-dark);
            border-radius: 10px;
        }
        .results pre::-webkit-scrollbar-thumb {
            background-color: var(--primary-red-light);
            border-radius: 10px;
            border: 3px solid var(--bg-dark);
        }


        .results button {
            background: linear-gradient(45deg, #4CAF50, #388E3C); /* Green gradient */
            color: white;
            padding: 16px 35px; /* More padding */
            border: none;
            border-radius: 10px; /* More rounded */
            cursor: pointer;
            margin-top: 30px; /* More space */
            font-size: 19px; /* Larger font */
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.4);
            display: block; /* Make it a block element to center */
            margin-left: auto;
            margin-right: auto;
            width: fit-content; /* Only take up necessary width */
        }

        .results button:hover {
            background: linear-gradient(45deg, #388E3C, #2E7D32); /* Darker green on hover */
            transform: translateY(-4px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.5);
        }
        .results button:active {
            transform: translateY(0);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.3);
        }

        /* Status Message */
        #statusMessage {
            text-align: center;
            margin-top: 35px; /* More spacing */
            padding: 18px; /* More padding */
            border-radius: 10px;
            font-weight: bold;
            display: none;
            font-size: 1.2em; /* Larger font */
            box-shadow: 0 5px 12px rgba(0, 0, 0, 0.3);
            border: 1px solid transparent; /* For consistent spacing */
        }

        #statusMessage.success {
            background-color: #28a745;
            color: white;
            border-color: #1e7e34;
        }

        #statusMessage.error {
            background-color: #dc3545;
            color: white;
            border-color: #bd2130;
        }

        #statusMessage.loading {
            background-color: #007bff;
            color: white;
            border-color: #0056b3;
        }

        /* New CSS for #nextSteps to be white */
        .results p#nextSteps {
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 20px;
            }
            .container {
                padding: 25px;
                border-radius: 20px;
            }
            
            .header {
                margin-bottom: 40px;
                padding: 30px 0;
            }
            .header h1 {
                font-size: 2.8em;
            }
            .header p {
                font-size: 1.3em;
            }

            .language-selector {
                margin-bottom: 30px;
            }
            .language-selector select {
                font-size: 18px;
                padding: 10px 15px; /* Reduced padding for mobile */
                background-position: right 10px center;
                background-size: 22px;
                height: auto;
            }

            .company-intro {
                padding: 30px;
                margin-bottom: 35px;
                gap: 20px;
            }
            .company-intro h2 {
                font-size: 2.2em;
            }
            .company-intro p {
                font-size: 1.1em;
            }
            .company-intro .company-logo {
                width: 100px;
                height: 100px;
                font-size: 2.5em;
            }

            .info-box {
                padding: 20px 25px;
                font-size: 1.05em;
                margin-bottom: 30px;
                border-radius: 15px;
            }

            .warning-box {
                padding: 25px;
                margin-bottom: 30px;
                border-radius: 15px;
                gap: 15px;
            }
            .warning-icon {
                width: 35px;
                height: 35px;
                font-size: 1.8em;
            }
            .warning-box h3 {
                font-size: 1.6em;
                margin-bottom: 15px;
                text-align: left;
                font-size: 1.6em;
            }
            .warning-box ul {
                margin-top: -5px;
            }
            .warning-box li {
                font-size: 1.05em;
                margin-bottom: 10px;
                padding-left: 20px;
            }
            .warning-box li::before {
                font-size: 1.1em;
            }

            .form-section {
                padding: 30px;
                margin-bottom: 35px;
                border-radius: 15px;
            }
            .form-section h2 {
                font-size: 1.8em;
                margin-bottom: 30px;
            }

            .form-group {
                margin-bottom: 30px;
            }
            .form-group label {
                font-size: 1.1em;
                margin-bottom: 12px;
            }
            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 17px;
                padding: 14px 18px; /* Reduced padding for mobile */
                border-radius: 12px;
                height: auto;
            }
            .form-group textarea {
                min-height: 150px;
            }

            .checkbox-group {
                gap: 20px;
                margin-top: 20px;
            }
            .checkbox-item {
                padding: 14px 22px;
                border-radius: 12px;
                min-width: unset;
            }
            .checkbox-item input[type="radio"],
            .checkbox-item input[type="checkbox"] { 
                width: 26px;
                height: 26px;
                margin-right: 15px;
            }
            .checkbox-item input[type="radio"]:checked::after,
            .checkbox-item input[type="checkbox"]:checked::after { 
                width: 13px; 
                height: 13px; 
            }
            .checkbox-item span {
                font-size: 1.05em;
            }

            .submit-btn {
                padding: 20px 45px;
                font-size: 22px;
                margin-top: 40px;
                border-radius: 12px;
            }
            .spinner {
                width: 26px;
                height: 26px;
            }

            .results {
                padding: 30px;
                margin-top: 40px;
                border-radius: 15px;
            }
            .results h3 {
                font-size: 1.6em;
                margin-bottom: 20px;
            }
            .results pre {
                padding: 25px;
                font-size: 1em;
                line-height: 1.7;
                max-height: 400px;
            }
            .results button {
                padding: 12px 25px;
                font-size: 17px;
                margin-top: 25px;
            }
            .results p {
                font-size: 1.1em;
                margin-top: 25px;
            }
            #statusMessage {
                padding: 15px;
                font-size: 1.1em;
                margin-top: 25px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            .container {
                padding: 20px;
                border-radius: 18px;
            }

            .header {
                margin-bottom: 30px;
                padding: 25px 0;
            }
            .header h1 {
                font-size: 2.2em;
                margin-bottom: 10px;
            }
            .header p {
                font-size: 1.1em;
            }
            .header::after {
                width: 60px;
                height: 2px;
            }

            .language-selector {
                margin-bottom: 25px;
            }
            .language-selector select {
                font-size: 16px;
                padding: 8px 12px; /* Even smaller padding for very small screens */
                background-position: right 8px center;
                background-size: 20px;
                min-width: 180px;
                height: auto;
            }

            .company-intro {
                padding: 20px;
                margin-bottom: 30px;
                gap: 15px;
            }
            .company-intro h2 {
                font-size: 1.8em;
            }
            .company-intro p {
                font-size: 1em;
            }
            .company-intro .company-logo {
                width: 80px;
                height: 80px;
                font-size: 2em;
            }

            .info-box {
                padding: 15px 20px;
                font-size: 0.95em;
                margin-bottom: 25px;
                border-radius: 12px;
            }

            .warning-box {
                padding: 20px;
                margin-bottom: 25px;
                border-radius: 12px;
                flex-direction: column; /* Stack icon and text on very small screens */
                align-items: center;
                text-align: center;
            }
            .warning-icon {
                width: 30px;
                height: 30px;
                font-size: 1.5em;
                margin-bottom: 10px;
            }
            .warning-box h3 {
                font-size: 1.6em;
                margin-bottom: 10px;
                text-align: center;
            }
            .warning-box ul {
                margin-top: 0;
                text-align: left; /* Keep list items left-aligned within the stacked box */
            }
            .warning-box li {
                font-size: 0.95em;
                margin-bottom: 10px;
                padding-left: 20px;
            }
            .warning-box li::before {
                font-size: 1em;
            }

            .form-section {
                padding: 25px;
                margin-bottom: 30px;
                border-radius: 12px;
            }
            .form-section h2 {
                font-size: 1.6em;
                margin-bottom: 25px;
            }

            .form-group {
                margin-bottom: 25px;
            }
            .form-group label {
                font-size: 1em;
                margin-bottom: 10px;
            }
            .form-group input,
            .form-group select,
            .form-group textarea {
                font-size: 15px;
                padding: 12px 15px; /* Further reduced padding for very small screens */
                border-radius: 10px;
                height: auto;
            }
            .form-group textarea {
                min-height: 120px;
            }

            .checkbox-group {
                gap: 15px;
                margin-top: 15px;
            }
            .checkbox-item {
                padding: 12px 18px;
                border-radius: 10px;
                flex-basis: 100%;
            }
            .checkbox-item input[type="radio"],
            .checkbox-item input[type="checkbox"] { 
                width: 24px;
                height: 24px;
                margin-right: 12px;
            }
            .checkbox-item input[type="radio"]:checked::after,
            .checkbox-item input[type="checkbox"]:checked::after { 
                width: 12px; 
                height: 12px; 
            }
            .checkbox-item span {
                font-size: 1em;
            }

            .submit-btn {
                padding: 18px 35px;
                font-size: 20px;
                margin-top: 35px;
                border-radius: 10px;
            }
            .spinner {
                width: 24px;
                height: 24px;
            }

            .results {
                padding: 25px;
                margin-top: 35px;
                border-radius: 12px;
            }
            .results h3 {
                font-size: 1.4em;
                margin-bottom: 18px;
            }
            .results pre {
                padding: 20px;
                font-size: 0.9em;
                line-height: 1.6;
                max-height: 350px;
            }
            .results button {
                padding: 10px 20px;
                font-size: 16px;
                margin-top: 20px;
            }
            .results p {
                font-size: 1em;
                margin-top: 20px;
            }
            #statusMessage {
                padding: 12px;
                font-size: 1em;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MEWAYZ Partnership</h1>
            <p>Strategic Partnership Vetting Form</p>
        </div>

        <div class="language-selector">
            <select id="languageSelect" onchange="changeLanguage()">
                <option value="en">English</option>
                <option value="nl">Nederlands</option>
                <option value="th">ไทย</option>
                <option value="ur">اردو</option>
                <option value="hi">हिंदी</option>
                <option value="es">Español</option>
                <option value="fr">Français</option>
                <option value="de">Deutsch</option>
                <option value="ar">العربية</option>
                <option value="zh">中文</option>
                <option value="ja">日本語</option>
            </select>
        </div>

        <!-- Company Introduction Section -->
        <div class="company-intro">
            <div class="company-logo">
                <!-- Placeholder image for Mewayz logo -->
                <img src="https://f005.backblazeb2.com/file/mewayz/mewayz-web/assets/Untitled%20design%20%2868%29.png" alt="Mewayz Logo">
            </div>
            <h2 id="companyIntroTitle">Welcome to Mewayz!</h2>
            <p id="companyIntroText">
                Mewayz is an innovative all-in-one platform designed to empower modern creators and online entrepreneurs. We provide a comprehensive suite of tools for selling digital products, building communities, creating courses, and managing businesses efficiently. Our mission is to simplify online entrepreneurship, allowing creators to focus on what they do best: creating value for their audience.
            </p>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <p id="infoBoxText">Don't know what we do? Or want to learn more? Go to <a href="https://mewayz.com">mewayz.com</a></p>
        </div>

        <div class="warning-box">
            <div class="warning-icon">!</div>
            <div>
                <h3 id="importantNote">IMPORTANT - READ CAREFULLY</h3>
                <ul id="warningList">
                    <li>This is an EQUITY-BASED partnership opportunity (% ownership)</li>
                    <li>Mewayz has 30,000+ users, was #1 on Product Hunt, and has $30,000 invested</li>
                    <li>We have $30,000 allocated for marketing/growth</li>
                    <li>Previous team received offers from ex-Meta/Apple developers (45% total equity requested)</li>
                    <li>If you're looking for immediate salary/payment, this opportunity is NOT for you</li>
                    <li>Only serious candidates who believe in equity partnerships should proceed</li>
                </ul>
            </div>
        </div>

        <form id="vettingForm" method="POST"> {{-- Removed action attribute --}}
            <!-- Laravel CSRF token (important for security) -->
            @csrf 
            <!-- Confirmation Section -->
            <div class="form-section">
                <h2 id="confirmationTitle">Opportunity Confirmation</h2>
                <div class="form-group">
                    <label id="confirmLabel">Do you confirm this is about the Mewayz equity partnership opportunity? *</label>
                    <select name="confirmation" id="confirmation" required>
                        <option value="">Select...</option>
                        <option value="yes" id="confirmYes">Yes, I understand this is about equity partnership</option>
                        <option value="no" id="confirmNo">No, I was expecting something different</option>
                    </select>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="form-section">
                <h2 id="personalTitle">Personal Information</h2>
                <div class="form-group">
                    <label id="nameLabel">Full Name *</label>
                    <input type="text" name="fullName" id="fullName" required>
                </div>
                <div class="form-group">
                    <label id="emailLabel">Email Address *</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label id="locationLabel">Location (Country/City) *</label>
                    <input type="text" name="location" id="location" required>
                </div>
                <div class="form-group">
                    <label id="linkedinLabel">LinkedIn Profile</label>
                    <input type="url" name="linkedin" id="linkedin">
                </div>
            </div>

            <!-- Experience & Skills -->
            <div class="form-section">
                <h2 id="experienceTitle">Experience & Skills</h2>
                <div class="form-group">
                    <label id="roleInterestLabel">Which role interests you most? *</label>
                    <select name="roleInterest" id="roleInterest" required>
                        <option value="">Select...</option>
                        <option value="product" id="productRole">Product Strategy Lead</option>
                        <option value="partnerships" id="partnershipsRole">Strategic Partnerships</option>
                        <option value="development" id="devRole">Product Development / Programming</option>
                        <option value="social_media" id="socialMediaRole">Social Media & Community Management</option>
                        <option value="content_copy" id="contentCopyRole">Content & Copywriting Lead</option>
                        <option value="data_analytics" id="dataAnalyticsRole">Data & Analytics Specialist</option>
                        <option value="sales_lead" id="salesLeadRole">Sales Lead</option>
                        <option value="marketing_lead" id="marketingLeadRole">Marketing Lead</option>
                        <option value="operations_lead" id="operationsLeadRole">Operations Lead</option>
                        <option value="legal_counsel" id="legalCounselRole">Legal Counsel</option>
                        <option value="ui_ux_designer" id="uiUxDesignerRole">UI/UX Designer</option>
                        <option value="data_scientist" id="dataScientistRole">Data Scientist</option>
                        <option value="customer_success" id="customerSuccessRole">Customer Success Lead</option>
                        <option value="finance_controller" id="financeControllerRole">Finance Controller</option>
                        <option value="business_analyst" id="businessAnalystRole">Business Analyst</option>
                        <option value="growth_lead" id="growthLeadRole">Growth Lead</option>
                        <option value="all" id="allRoles">All roles interest me</option>
                    </select>
                </div>
                <div class="form-group">
                    <label id="experienceYearsLabel">Years of relevant experience? *</label>
                    <select name="experienceYears" id="experienceYears" required>
                        <option value="">Select...</option>
                        <option value="0-2" id="exp0_2">0-2 years</option>
                        <option value="3-5" id="exp3_5">3-5 years</option>
                        <option value="6-10" id="exp6_10">6-10 years</option>
                        <option value="10+" id="exp10_plus">10+ years</option>
                    </select>
                </div>
                <div class="form-group">
                    <label id="skillsLabel">Select your key skills (select one or more):</label> 
                    <div class="checkbox-group">
                        <label class="checkbox-item" for="skillProductStrategy">
                            <input type="checkbox" name="skill[]" value="product-strategy" id="skillProductStrategy">
                            <span id="skill1">Product Strategy</span>
                        </label>
                        <label class="checkbox-item" for="skillBusinessDevelopment">
                            <input type="checkbox" name="skill[]" value="business-development" id="skillBusinessDevelopment">
                            <span id="skill2">Business Development</span>
                        </label>
                        <label class="checkbox-item" for="skillPartnerships">
                            <input type="checkbox" name="skill[]" value="partnerships" id="skillPartnerships">
                            <span id="skill3">Strategic Partnerships</span>
                        </label>
                        <label class="checkbox-item" for="skillMarketing">
                            <input type="checkbox" name="skill[]" value="marketing" id="skillMarketing">
                            <span id="skill4">Marketing/Growth</span>
                        </label>
                        <label class="checkbox-item" for="skillTech">
                            <input type="checkbox" name="skill[]" value="tech" id="skillTech">
                            <span id="skill5">Technical/Development/Programming</span>
                        </label>
                        <label class="checkbox-item" for="skillFundraising">
                            <input type="checkbox" name="skill[]" value="fundraising" id="skillFundraising">
                            <span id="skill6">Fundraising</span>
                        </label>
                        <label class="checkbox-item" for="skillAI">
                            <input type="checkbox" name="skill[]" value="ai" id="skillAI">
                            <span id="skill7">AI/Machine Learning</span>
                        </label>
                        <label class="checkbox-item" for="skillEcommerce">
                            <input type="checkbox" name="skill[]" value="ecommerce" id="skillEcommerce">
                            <span id="skill8">E-commerce Platforms</span>
                        </label>
                        <label class="checkbox-item" for="skillCRM">
                            <input type="checkbox" name="skill[]" value="crm" id="skillCRM">
                            <span id="skill9">CRM Systems</span>
                        </label>
                        <label class="checkbox-item" for="skillCommunity">
                            <input type="checkbox" name="skill[]" value="community" id="skillCommunity">
                            <span id="skill10">Community Building</span>
                        </label>
                        <label class="checkbox-item" for="skillCopywriting">
                            <input type="checkbox" name="skill[]" value="copywriting" id="skillCopywriting">
                            <span id="skill11">Copywriting</span>
                        </label>
                        <label class="checkbox-item" for="skillSEO">
                            <input type="checkbox" name="skill[]" value="seo" id="skillSEO">
                            <span id="skill12">SEO Optimization</span>
                        </label>
                        <label class="checkbox-item" for="skillVideoEditing">
                            <input type="checkbox" name="skill[]" value="video-editing" id="skillVideoEditing">
                            <span id="skill13">Video Editing</span>
                        </label>
                        <label class="checkbox-item" for="skillGraphicDesign">
                            <input type="checkbox" name="skill[]" value="graphic-design" id="skillGraphicDesign">
                            <span id="skill14">Graphic Design</span>
                        </label>
                        <label class="checkbox-item" for="skillDataAnalysis">
                            <input type="checkbox" name="skill[]" value="data-analysis" id="skillDataAnalysis">
                            <span id="skill15">Data Analysis</span>
                        </label>
                        <label class="checkbox-item" for="skillProjectManagement">
                            <input type="checkbox" name="skill[]" value="project-management" id="skillProjectManagement">
                            <span id="skill16">Project Management</span>
                        </label>
                        <label class="checkbox-item" for="skillLegal">
                            <input type="checkbox" name="skill[]" value="legal" id="skillLegal">
                            <span id="skill17">Legal & Compliance</span>
                        </label>
                        <label class="checkbox-item" for="skillSales">
                            <input type="checkbox" name="skill[]" value="sales" id="skillSales">
                            <span id="skill18">Sales Strategy</span>
                        </label>
                        <label class="checkbox-item" for="skillFinance">
                            <input type="checkbox" name="skill[]" value="finance" id="skillFinance">
                            <span id="skill19">Finance & Accounting</span>
                        </label>
                        <label class="checkbox-item" for="skillHR">
                            <input type="checkbox" name="skill[]" value="hr" id="skillHR">
                            <span id="skill20">Human Resources</span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label id="companiesLabel">Previous companies/notable achievements *</label>
                    <textarea name="previousCompanies" id="previousCompanies" required placeholder="List companies you've worked for, startups you've built, major achievements, etc."></textarea>
                </div>
            </div>

            <!-- Compensation Expectations -->
            <div class="form-section">
                <h2 id="compensationTitle">Compensation & Commitment</h2>
                <div class="form-group">
                    <label id="partnershipTypeLabel">What type of partnership are you seeking? *</label>
                    <div class="checkbox-group">
                        <label class="checkbox-item">
                            <input type="radio" name="partnershipType" value="equity" id="partnershipTypeEquity" checked>
                            <span id="partnershipType1">Equity-Based Partnership</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="radio" name="partnershipType" value="hybrid" id="partnershipTypeHybrid">
                            <span id="partnershipType2">Hybrid (Salary + Equity)</span>
                        </label>
                        <label class="checkbox-item">
                            <input type="radio" name="partnershipType" value="monetary_business" id="partnershipTypeMonetaryBusiness">
                            <span id="partnershipType3">Monetary Compensation for Business/Portfolio</span>
                        </label>
                    </div>
                </div>

                <div id="equityCompensationGroup">
                    <div class="form-group">
                        <label id="equityAcceptanceLabel">Are you comfortable with equity-only compensation initially? *</label>
                        <select name="equityAcceptance" id="equityAcceptance">
                            <option value="">Select...</option>
                            <option value="yes-full" id="equityYesFull">Yes, I'm fully comfortable with equity-only</option>
                            <option value="yes-conditions" id="equityYesConditions">Yes, but with some conditions</option>
                            <option value="hybrid" id="equityHybrid">I prefer a hybrid model (small salary + equity)</option>
                            <option value="no" id="equityNo">No, I need immediate payment</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label id="equityExpectationLabel">What equity percentage seems fair for your contribution? *</label>
                        <select name="equityExpectation" id="equityExpectation">
                            <option value="">Select...</option>
                            <option value="<1%" id="equityLess1"><1%</option>
                            <option value="1-2%" id="equity1_2">1-2%</option> {{-- New granular option --}}
                            <option value="2-5%" id="equity2_5">2-5%</option> {{-- New granular option --}}
                            <option value="5-10%" id="equity5_10">5-10%</option>
                            <option value="10-15%" id="equity10_15">10-15%</option>
                            <option value="15-20%" id="equity15_20">15-20%</option>
                            <option value="20-25%" id="equity20_25">20-25%</option>
                            <option value="25%+" id="equity25_plus">25%+</option>
                            <option value="negotiable" id="negotiable">Open to negotiation based on value</option>
                        </select>
                    </div>
                    {{-- New field for monthly revenue share for hybrid model --}}
                    <div class="form-group" id="hybridRevenueShareGroup" style="display: none;">
                        <label id="monthlyRevenueShareLabel">Desired Monthly Revenue Share (USD) for Hybrid Model *</label>
                        <input type="number" name="monthlyRevenueShare" id="monthlyRevenueShare" placeholder="e.g., 500">
                    </div>
                </div>

                <div id="monetaryCompensationGroup" style="display: none;">
                    <div class="form-group">
                        <label id="upfrontFeeLabel">Desired Upfront Fee (USD) *</label>
                        <input type="number" name="upfrontFee" id="upfrontFee" placeholder="e.g., 5000">
                    </div>
                    <div class="form-group">
                        <label id="monthlyFeeLabel">Desired Monthly Fee (USD) *</label>
                        <input type="number" name="monthlyFee" id="monthlyFee" placeholder="e.g., 1000">
                    </div>
                    <div class="form-group">
                        <label id="businessDescriptionLabel">Describe the Business/Portfolio you are offering *</label>
                        <textarea name="businessDescription" id="businessDescription" placeholder="Provide details about your business, its assets, revenue, user base, or the portfolio of work you are offering for monetary compensation."></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label id="timeCommitmentLabel">Time commitment you can offer? *</label>
                    <select name="timeCommitment" id="timeCommitment" required>
                        <option value="">Select...</option>
                        <option value="part-time" id="timePartTime">Part-time (10-20 hours/week)</option>
                        <option value="substantial" id="timeSubstantial">Substantial (30-40 hours/week)</option>
                        <option value="full-time" id="timeFullTime">Full-time commitment</option>
                        <option value="flexible" id="flexible">Flexible based on needs</option>
                    </select>
                </div>
            </div>

            <!-- Strategic Questions -->
            <div class="form-section">
                <h2 id="strategicTitle">Strategic Questions</h2>
                <div class="form-group">
                    <label id="platformToolExpLabel">Experience with Mewayz-like platform features or relevant tools? *</label>
                    <textarea name="platformToolExperience" id="platformToolExperience" required placeholder="Detail your experience with platforms like Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite, etc., or specific features such as E-commerce, CRM, Course Platforms, Social Media Management, AI automation, or Escrow Systems."></textarea>
                </div>
                <div class="form-group">
                    <label id="creatorEntrepreneurExpLabel">Experience working with 'Modern Creators' or 'Online Entrepreneurs'? *</label>
                    <textarea name="creatorEntrepreneurExperience" id="creatorEntrepreneurExperience" required placeholder="Describe your experience in content creation, online coaching, digital marketing, or managing an online business."></textarea>
                </div>
                <div class="form-group">
                    <label id="mewayzVisionLabel">Your strategic vision for Mewayz's growth? *</label>
                    <textarea name="mewayzGrowthVision" id="mewayzGrowthVision" required placeholder="Consider specific features like AI-powered automation, integrations (Zapier, payment gateways), or enhancing community building. Be concise."></textarea>
                </div>
                <div class="form-group">
                    <label id="networkLabel">Describe your professional network *</label>
                    <textarea name="network" id="network" required placeholder="What connections do you have that could benefit Mewayz? (investors, partners, clients, etc.)"></textarea>
                </div>
                <div class="form-group">
                    <label id="valuePropositionLabel">What unique value would you bring to Mewayz? *</label>
                    <textarea name="valueProposition" id="valueProposition" required placeholder="Be specific about what makes you the right partner"></textarea>
                </div>
                <div class="form-group">
                    <label id="challengesLabel">What do you see as the biggest challenges for an all-in-one business platform like Mewayz?</label>
                    <textarea name="challenges" id="challenges" placeholder="Share your perspective on industry challenges and how Mewayz can overcome them."></textarea>
                </div>
            </div>

            <!-- Final Assessment -->
            <div class="form-section">
                <h2 id="finalTitle">Final Assessment</h2>
                <div class="form-group">
                    <label id="whyNowLabel">Why are you interested in joining Mewayz now? *</label>
                    <textarea name="whyNow" id="whyNow" required placeholder="What attracts you to this opportunity at this stage?"></textarea>
                </div>
                <div class="form-group">
                    <label id="financialLabel">Current financial situation (honest assessment) *</label>
                    <select name="financialSituation" id="financialSituation" required>
                        <option value="">Select...</option>
                        <option value="stable" id="financialStable">Financially stable, can work for equity</option>
                        <option value="some-runway" id="financialRunway">Have some runway, prefer hybrid</option>
                        <option value="need-income" id="financialNeed">Need immediate income</option>
                        <option value="prefer-not-say" id="financialPrivate">Prefer not to say</option>
                    </select>
                </div>
                <div class="form-group">
                    <label id="availabilityLabel">When could you start? *</label>
                    <select name="availability" id="availability" required>
                        <option value="">Select...</option>
                        <option value="immediately" id="availableNow">Immediately</option>
                        <option value="1-2-weeks" id="avail1_2weeks">1-2 weeks</option>
                        <option value="1-month" id="avail1_month">Within 1 month</option>
                        <option value="longer" id="availableLater">Longer timeline needed</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                <span id="submitBtnText">Submit Application</span>
                <div class="spinner" id="submitSpinner"></div>
            </button>
            <div id="statusMessage"></div>
        </form>

        <div id="results" class="results">
            <h3 id="resultsTitle">Application Summary</h3>
            <pre id="resultsContent"></pre>
            <button id="copyReportBtn">Copy Report</button>
            <p id="nextSteps">We'll review your application and get back to you within 48 hours if there's a potential fit.</p>
        </div>
    </div>

  <script>
        const translations = {
            en: {
                companyIntroTitle: "Welcome to Mewayz!",
                companyIntroText: "Mewayz is an innovative all-in-one platform designed to empower modern creators and online entrepreneurs. We provide a comprehensive suite of tools for selling digital products, building communities, creating courses, and managing businesses efficiently. Our mission is to simplify online entrepreneurship, allowing creators to focus on what they do best: creating value for their audience.",
                infoBoxText: "Don't know what we do? Or want to learn more? Go to <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "IMPORTANT - READ CAREFULLY",
                warningList: [
                    "This is an EQUITY-BASED partnership opportunity (% ownership)",
                    "Mewayz has 30,000+ users, was #1 on Product Hunt, and has $30,000 invested",
                    "We have $30,000 allocated for marketing/growth",
                    "Previous team received offers from ex-Meta/Apple developers (45% total equity requested)",
                    "If you're looking for immediate salary/payment, this opportunity is NOT for you",
                    "Only serious candidates who believe in equity partnerships should proceed"
                ],
                confirmationTitle: "Opportunity Confirmation",
                confirmLabel: "Do you confirm this is about the Mewayz equity partnership opportunity? *",
                confirmYes: "Yes, I understand this is about equity partnership",
                confirmNo: "No, I was expecting something different",
                personalTitle: "Personal Information",
                nameLabel: "Full Name *",
                emailLabel: "Email Address *",
                locationLabel: "Location (Country/City) *",
                linkedinLabel: "LinkedIn Profile",
                experienceTitle: "Experience & Skills",
                roleInterestLabel: "Which role interests you most? *",
                productRole: "Product Strategy Lead",
                partnershipsRole: "Strategic Partnerships",
                devRole: "Product Development / Programming",
                socialMediaRole: "Social Media & Community Management",
                contentCopyRole: "Content & Copywriting Lead",
                dataAnalyticsRole: "Data & Analytics Specialist",
                salesLeadRole: "Sales Lead",
                marketingLeadRole: "Marketing Lead",
                operationsLeadRole: "Operations Lead",
                legalCounselRole: "Legal Counsel",
                uiUxDesignerRole: "UI/UX Designer",
                dataScientistRole: "Data Scientist",
                customerSuccessRole: "Customer Success Lead",
                financeControllerRole: "Finance Controller",
                businessAnalystRole: "Business Analyst",
                growthLeadRole: "Growth Lead",
                allRoles: "All roles interest me",
                experienceYearsLabel: "Years of relevant experience? *",
                exp0_2: "0-2 years",
                exp3_5: "3-5 years",
                exp6_10: "6-10 years",
                exp10_plus: "10+ years",
                skillsLabel: "Select your key skills (select one or more):", 
                skill1: "Product Strategy",
                skill2: "Business Development",
                skill3: "Strategic Partnerships",
                skill4: "Marketing/Growth",
                skill5: "Technical/Development/Programming",
                skill6: "Fundraising",
                skill7: "AI/Machine Learning",
                skill8: "E-commerce Platforms",
                skill9: "CRM Systems",
                skill10: "Community Building",
                skill11: "Copywriting",
                skill12: "SEO Optimization",
                skill13: "Video Editing",
                skill14: "Graphic Design",
                skill15: "Data Analysis",
                skill16: "Project Management",
                skill17: "Legal & Compliance",
                skill18: "Sales Strategy",
                skill19: "Finance & Accounting",
                skill20: "Human Resources",
                companiesLabel: "Previous companies/notable achievements *",
                previousCompaniesPlaceholder: "List companies you've worked for, startups you've built, major achievements, etc.",
                compensationTitle: "Compensation & Commitment",
                partnershipTypeLabel: "What type of partnership are you seeking? *",
                partnershipType1: "Equity-Based Partnership",
                partnershipType2: "Hybrid (Salary + Equity)",
                partnershipType3: "Monetary Compensation for Business/Portfolio",
                upfrontFeeLabel: "Desired Upfront Fee (USD) *",
                upfrontFeePlaceholder: "e.g., 5000",
                monthlyFeeLabel: "Desired Monthly Fee (USD) *",
                monthlyFeePlaceholder: "e.g., 1000",
                businessDescriptionLabel: "Describe the Business/Portfolio you are offering *",
                businessDescriptionPlaceholder: "Provide details about your business, its assets, revenue, user base, or the portfolio of work you are offering for monetary compensation.",
                monthlyRevenueShareLabel: "Desired Monthly Revenue Share (USD) for Hybrid Model *",
                monthlyRevenueSharePlaceholder: "e.g., 500",
                equityAcceptanceLabel: "Are you comfortable with equity-only compensation initially? *",
                equityYesFull: "Yes, I'm fully comfortable with equity-only",
                equityYesConditions: "Yes, but with some conditions",
                equityHybrid: "I prefer a hybrid model (small salary + equity)",
                equityNo: "No, I need immediate payment",
                equityExpectationLabel: "What equity percentage seems fair for your contribution? *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "Open to negotiation based on value",
                timeCommitmentLabel: "Time commitment you can offer? *",
                timePartTime: "Part-time (10-20 hours/week)",
                timeSubstantial: "Substantial (30-40 hours/week)",
                timeFullTime: "Full-time commitment",
                flexible: "Flexible based on needs",
                strategicTitle: "Strategic Questions",
                platformToolExpLabel: "Experience with Mewayz-like platform features or relevant tools? *",
                platformToolExperiencePlaceholder: "Detail your experience with platforms like Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite, etc., or specific features such as E-commerce, CRM, Course Platforms, Social Media Management, AI automation, or Escrow Systems.",
                creatorEntrepreneurExpLabel: "Experience working with 'Modern Creators' or 'Online Entrepreneurs'? *",
                creatorEntrepreneurExperiencePlaceholder: "Describe your experience in content creation, online coaching, digital marketing, or managing an online business.",
                mewayzVisionLabel: "Your strategic vision for Mewayz's growth? *",
                mewayzGrowthVisionPlaceholder: "Consider specific features like AI-powered automation, integrations (Zapier, payment gateways), or enhancing community building. Be concise.",
                networkLabel: "Describe your professional network *",
                networkPlaceholder: "What connections do you have that could benefit Mewayz? (investors, partners, clients, etc.)",
                valuePropositionLabel: "What unique value would you bring to Mewayz? *",
                valuePropositionPlaceholder: "Be specific about what makes you the right partner",
                challengesLabel: "What do you see as the biggest challenges for an all-in-one business platform like Mewayz?",
                challengesPlaceholder: "Share your perspective on industry challenges and how Mewayz can overcome them.",
                finalTitle: "Final Assessment",
                whyNowLabel: "Why are you interested in joining Mewayz now? *",
                whyNowPlaceholder: "What attracts you to this opportunity at this stage?",
                financialLabel: "Current financial situation (honest assessment) *",
                financialStable: "Financially stable, can work for equity",
                financialRunway: "Have some runway, prefer hybrid",
                financialNeed: "Need immediate income",
                financialPrivate: "Prefer not to say",
                availabilityLabel: "When could you start? *",
                availableNow: "Immediately",
                avail1_2weeks: "1-2 weeks",
                avail1_month: "Within 1 month",
                availableLater: "Longer timeline needed",
                submitBtn: "Submit Application",
                resultsTitle: "Application Summary",
                nextSteps: "We'll review your application and get back to you within 48 hours if there's a potential fit."
            },
            nl: {
                companyIntroTitle: "Welkom bij Mewayz!",
                companyIntroText: "Mewayz is een innovatief alles-in-één platform dat is ontworpen om moderne makers en online ondernemers te ondersteunen. We bieden een uitgebreide reeks tools voor het verkopen van digitale producten, het opbouwen van communities, het maken van cursussen en het efficiënt beheren van bedrijven. Onze missie is om online ondernemerschap te vereenvoudigen, zodat makers zich kunnen concentreren op waar ze het beste in zijn: waarde creëren voor hun publiek.",
                infoBoxText: "Weet u niet wat we doen? Of wilt u meer weten? Ga naar <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "BELANGRIJK - LEES AANDACHTIG",
                warningList: [
                    "Dit is een op EIGENDOM gebaseerde samenwerkingsmogelijkheid (% eigendom)",
                    "Mewayz heeft 30.000+ gebruikers, was #1 op Product Hunt en heeft $30.000 geïnvesteerd",
                    "We hebben $30,000 toegewezen voor marketing/groei",
                    "Vorig team ontving aanbiedingen van ex-Meta/Apple ontwikkelaars (45% totale aandelen gevraagd)",
                    "Als je op zoek bent naar direct salaris/betaling, is deze mogelijkheid NIET voor jou",
                    "Alleen serieuze kandidaten die geloven in partnerschappen op basis van aandelen moeten doorgaan"
                ],
                confirmationTitle: "Bevestiging Gelegenheid",
                confirmLabel: "Bevestigt u dat dit gaat over de Mewayz aandelenpartnerschap mogelijkheid? *",
                confirmYes: "Ja, ik begrijp dat dit over aandelenpartnerschap gaat",
                confirmNo: "Nee, ik verwachtte iets anders",
                personalTitle: "Persoonlijke Informatie",
                nameLabel: "Volledige Naam *",
                emailLabel: "E-mailadres *",
                locationLabel: "Locatie (Land/Stad) *",
                linkedinLabel: "LinkedIn Profiel",
                experienceTitle: "Ervaring & Vaardigheden",
                roleInterestLabel: "Welke rol interesseert u het meest? *",
                productRole: "Product Strategie Leider",
                partnershipsRole: "Strategische Partnerschappen",
                devRole: "Productontwikkeling / Programmeren",
                socialMediaRole: "Sociale Media & Community Management",
                contentCopyRole: "Content & Copywriting Leider",
                dataAnalyticsRole: "Data & Analyse Specialist",
                salesLeadRole: "Verkoop Lead",
                marketingLeadRole: "Marketing Lead",
                operationsLeadRole: "Operatie Lead",
                legalCounselRole: "Juridisch Adviseur",
                uiUxDesignerRole: "UI/UX Ontwerper",
                dataScientistRole: "Data Wetenschapper",
                customerSuccessRole: "Klant Succes Lead",
                financeControllerRole: "Financiële Controller",
                businessAnalystRole: "Business Analist",
                growthLeadRole: "Groei Lead",
                allRoles: "Alle rollen interesseren me",
                experienceYearsLabel: "Jaren relevante ervaring? *",
                exp0_2: "0-2 jaar",
                exp3_5: "3-5 jaar",
                exp6_10: "6-10 jaar",
                exp10_plus: "10+ jaar",
                skillsLabel: "Selecteer uw belangrijkste vaardigheden (selecteer een of meer):", 
                skill1: "Productstrategie",
                skill2: "Bedrijfsontwikkeling",
                skill3: "Strategische Partnerschappen",
                skill4: "Marketing/Groei",
                skill5: "Technisch/Ontwikkeling/Programmeren",
                skill6: "Fondsenwerving",
                skill7: "AI/Machine Learning",
                skill8: "E-commerce Platforms",
                skill9: "CRM Systemen",
                skill10: "Community Building",
                skill11: "Copywriting",
                skill12: "SEO Optimalisatie",
                skill13: "Video Bewerking",
                skill14: "Grafisch Ontwerp",
                skill15: "Data Analyse",
                skill16: "Project Management",
                skill17: "Juridisch & Compliance",
                skill18: "Verkoop Strategie",
                skill19: "Financiën & Boekhouding",
                skill20: "Human Resources",
                companiesLabel: "Vorige bedrijven/opmerkelijke prestaties *",
                previousCompaniesPlaceholder: "Lijst van bedrijven waar u heeft gewerkt, startups die u heeft opgebouwd, belangrijke prestaties, enz.",
                compensationTitle: "Compensatie & Inzet",
                partnershipTypeLabel: "Welk type partnerschap zoekt u? *",
                partnershipType1: "Partnerschap op basis van aandelen",
                partnershipType2: "Hybride (salaris + aandelen)",
                partnershipType3: "Monetaire vergoeding voor bedrijf/portfolio",
                upfrontFeeLabel: "Gewenste eenmalige vergoeding (USD) *",
                upfrontFeePlaceholder: "bijv. 5000",
                monthlyFeeLabel: "Gewenste maandelijkse vergoeding (USD) *",
                monthlyFeePlaceholder: "bijv. 1000",
                businessDescriptionLabel: "Beschrijf het bedrijf/portfolio dat u aanbiedt *",
                businessDescriptionPlaceholder: "Geef details over uw bedrijf, activa, omzet, gebruikersbestand of het werkportfolio dat u aanbiedt voor monetaire vergoeding.",
                monthlyRevenueShareLabel: "Gewenste maandelijkse omzetaandeel (USD) voor Hybride Model *",
                monthlyRevenueSharePlaceholder: "bijv. 500",
                equityAcceptanceLabel: "Bent u comfortabel met een initiële vergoeding uitsluitend in aandelen? *",
                equityYesFull: "Ja, ik ben volledig comfortabel met alleen aandelen",
                equityYesConditions: "Ja, maar met enkele voorwaarden",
                equityHybrid: "Ik geef de voorkeur aan een hybride model (klein salaris + aandelen)",
                equityNo: "Nee, ik heb direct betaling nodig",
                equityExpectationLabel: "Welk percentage aandelen lijkt redelijk voor uw bijdrage? *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "Open voor onderhandeling op basis van waarde",
                timeCommitmentLabel: "Tijdsinzet die u kunt bieden? *",
                timePartTime: "Parttime (10-20 uur/week)",
                timeSubstantial: "Substantieel (30-40 uur/week)",
                timeFullTime: "Fulltime inzet",
                flexible: "Flexibel op basis van behoeften",
                strategicTitle: "Strategische Vragen",
                platformToolExpLabel: "Ervaring met Mewayz-achtige platformfuncties of relevante tools? *",
                platformToolExperiencePlaceholder: "Beschrijf uw ervaring met platforms zoals Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite, enz., of specifieke functies zoals E-commerce, CRM, Course Platforms, Social Media Management, AI-automatisering, of Escrow-systemen.",
                creatorEntrepreneurExpLabel: "Ervaring met 'moderne makers' of 'online ondernemers'? *",
                creatorEntrepreneurExperiencePlaceholder: "Beschrijf uw ervaring met contentcreatie, online coaching, digitale marketing of het beheren van een online bedrijf.",
                mewayzVisionLabel: "Uw strategische visie voor de groei van Mewayz? *",
                mewayzGrowthVisionPlaceholder: "Denk aan specifieke functies zoals AI-gestuurde automatisering, integraties (Zapier, betalingsgateways) of het verbeteren van community building. Wees beknopt.",
                networkLabel: "Beschrijf uw professionele netwerk *",
                networkPlaceholder: "Welke connecties heeft u die Mewayz ten goede kunnen komen? (investeerders, partners, klanten, etc.)",
                valuePropositionLabel: "Welke unieke waarde zou u Mewayz brengen? *",
                valuePropositionPlaceholder: "Wees specifiek over wat u de juiste partner maakt",
                challengesLabel: "Wat ziet u als de grootste uitdagingen voor een alles-in-één bedrijfsplatform zoals Mewayz?",
                challengesPlaceholder: "Deel uw perspectief op uitdagingen in de sector en hoe Mewayz deze kan overwinnen.",
                finalTitle: "Eindbeoordeling",
                whyNowLabel: "Waarom bent u nu geïnteresseerd om deel te nemen aan Mewayz? *",
                whyNowPlaceholder: "Wat trekt u aan in deze gelegenheid in dit stadium?",
                financialLabel: "Huidige financiële situatie (eerlijke beoordeling) *",
                financialStable: "Financieel stabiel, kan voor aandelen werken",
                financialRunway: "Heb enige financiële ruimte, geef de voorkeur aan hybride",
                financialNeed: "Heb direct inkomen nodig",
                financialPrivate: "Liever niet zeggen",
                availabilityLabel: "Wanneer kunt u beginnen? *",
                availableNow: "Onmiddellijk",
                avail1_2weeks: "1-2 weken",
                avail1_month: "Binnen 1 maand",
                availableLater: "Langere termijn nodig",
                submitBtn: "Aanvraag indienen",
                resultsTitle: "Samenvatting Aanvraag",
                nextSteps: "We zullen uw aanvraag beoordelen en nemen binnen 48 uur contact met u op als er een mogelijke match is."
            },
            th: {
                companyIntroTitle: "ยินดีต้อนรับสู่ Mewayz!",
                companyIntroText: "Mewayz เป็นแพลตฟอร์มครบวงจรนวัตกรรมใหม่ที่ออกแบบมาเพื่อเสริมศักยภาพผู้สร้างสมัยใหม่และผู้ประกอบการออนไลน์ เรานำเสนอชุดเครื่องมือที่ครอบคลุมสำหรับการขายผลิตภัณฑ์ดิจิทัล, การสร้างชุมชน, การสร้างหลักสูตร, และการจัดการธุรกิจอย่างมีประสิทธิภาพ ภารกิจของเราคือการทำให้การเป็นผู้ประกอบการออนไลน์ง่ายขึ้น ช่วยให้ผู้สร้างมุ่งเน้นไปที่สิ่งที่พวกเขาทำได้ดีที่สุด: การสร้างคุณค่าให้กับผู้ชมของพวกเขา",
                infoBoxText: "ไม่รู้ว่าเราทำอะไร? หรือต้องการเรียนรู้เพิ่มเติม? ไปที่ <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "สำคัญ - โปรดอ่านอย่างละเอียด",
                warningList: [
                    "นี่คือโอกาสในการเป็นพันธมิตรแบบใช้ทุน (สัดส่วนการเป็นเจ้าของ)",
                    "Mewayz มีผู้ใช้มากกว่า 30,000 คน เคยเป็นอันดับ 1 บน Product Hunt และมีการลงทุน $30,000",
                    "เราได้จัดสรรเงิน $30,000 สำหรับการตลาด/การเติบโต",
                    "ทีมงานก่อนหน้านี้ได้รับข้อเสนอจากนักพัฒนาอดีต Meta/Apple (ขอสัดส่วนการเป็นเจ้าของรวม 45%)",
                    "หากคุณกำลังมองหาเงินเดือน/การชำระเงินทันที โอกาสนี้ไม่เหมาะสำหรับคุณ",
                    "เฉพาะผู้สมัครที่จริงจังที่เชื่อในการเป็นพันธมิตรแบบใช้ทุนเท่านั้นที่ควรดำเนินการต่อ"
                ],
                confirmationTitle: "การยืนยันโอกาส",
                confirmLabel: "คุณยืนยันหรือไม่ว่านี่คือโอกาสในการเป็นพันธมิตรด้านส่วนของผู้ถือหุ้นของ Mewayz? *",
                confirmYes: "ใช่ ฉันเข้าใจว่านี่คือการเป็นพันธมิตรด้านส่วนของผู้ถือหุ้น",
                confirmNo: "ไม่ ฉันคาดหวังบางอย่างที่แตกต่างออกไป",
                personalTitle: "ข้อมูลส่วนบุคคล",
                nameLabel: "ชื่อ-นามสกุล *",
                emailLabel: "ที่อยู่อีเมล *",
                locationLabel: "ที่ตั้ง (ประเทศ/เมือง) *",
                linkedinLabel: "โปรไฟล์ LinkedIn",
                experienceTitle: "ประสบการณ์และทักษะ",
                roleInterestLabel: "บทบาทใดที่คุณสนใจมากที่สุด? *",
                productRole: "หัวหน้าฝ่ายกลยุทธ์ผลิตภัณฑ์",
                partnershipsRole: "พันธมิตรเชิงกลยุทธ์",
                devRole: "การพัฒนาผลิตภัณฑ์ / การเขียนโปรแกรม",
                socialMediaRole: "การจัดการโซเชียลมีเดียและชุมชน",
                contentCopyRole: "หัวหน้าฝ่ายเนื้อหาและการเขียนคำโฆษณา",
                dataAnalyticsRole: "ผู้เชี่ยวชาญด้านข้อมูลและการวิเคราะห์",
                salesLeadRole: "หัวหน้าฝ่ายขาย",
                marketingLeadRole: "หัวหน้าฝ่ายการตลาด",
                operationsLeadRole: "หัวหน้าฝ่ายปฏิบัติการ",
                legalCounselRole: "ที่ปรึกษากฎหมาย",
                uiUxDesignerRole: "นักออกแบบ UI/UX",
                dataScientistRole: "นักวิทยาศาสตร์ข้อมูล",
                customerSuccessRole: "หัวหน้าฝ่ายความสำเร็จของลูกค้า",
                financeControllerRole: "ผู้ควบคุมการเงิน",
                businessAnalystRole: "นักวิเคราะห์ธุรกิจ",
                growthLeadRole: "หัวหน้าฝ่ายการเติบโต",
                allRoles: "ฉันสนใจทุกบทบาท",
                experienceYearsLabel: "ประสบการณ์ที่เกี่ยวข้องกี่ปี? *",
                exp0_2: "0-2 ปี",
                exp3_5: "3-5 ปี",
                exp6_10: "6-10 ปี",
                exp10_plus: "10+ ปี",
                skillsLabel: "เลือกทักษะหลักของคุณ (เลือกหนึ่งอย่างหรือมากกว่า):", 
                skill1: "กลยุทธ์ผลิตภัณฑ์",
                skill2: "การพัฒนาธุรกิจ",
                skill3: "พันธมิตรเชิงกลยุทธ์",
                skill4: "การตลาด/การเติบโต",
                skill5: "เทคนิค/การพัฒนา/การเขียนโปรแกรม",
                skill6: "การระดมทุน",
                skill7: "AI/Machine Learning",
                skill8: "E-commerce Platforms",
                skill9: "CRM Systems",
                skill10: "Community Building",
                skill11: "การเขียนคำโฆษณา",
                skill12: "การเพิ่มประสิทธิภาพ SEO",
                skill13: "การตัดต่อวิดีโอ",
                skill14: "การออกแบบกราฟิก",
                skill15: "การวิเคราะห์ข้อมูล",
                skill16: "การจัดการโครงการ",
                skill17: "กฎหมายและการปฏิบัติตามข้อกำหนด",
                skill18: "กลยุทธ์การขาย",
                skill19: "การเงินและการบัญชี",
                skill20: "ทรัพยากรบุคคล",
                companiesLabel: "บริษัทก่อนหน้า/ความสำเร็จที่โดดเด่น *",
                previousCompaniesPlaceholder: "ระบุชื่อบริษัทที่คุณเคยทำงานด้วย, สตาร์ทอัพที่คุณสร้าง, ความสำเร็จที่สำคัญ ฯลฯ",
                compensationTitle: "ค่าตอบแทนและความมุ่งมั่น",
                partnershipTypeLabel: "คุณกำลังมองหาประเภทพันธมิตรแบบใด? *",
                partnershipType1: "พันธมิตรแบบใช้ทุน",
                partnershipType2: "ไฮบริด (เงินเดือน + ทุน)",
                partnershipType3: "ค่าตอบแทนทางการเงินสำหรับธุรกิจ/พอร์ตโฟลิโอ",
                upfrontFeeLabel: "ค่าธรรมเนียมล่วงหน้าที่ต้องการ (USD) *",
                upfrontFeePlaceholder: "เช่น 5000",
                monthlyFeeLabel: "ค่าธรรมเนียมรายเดือนที่ต้องการ (USD) *",
                monthlyFeePlaceholder: "เช่น 1000",
                businessDescriptionLabel: "อธิบายธุรกิจ/พอร์ตโฟลิโอที่คุณเสนอ *",
                businessDescriptionPlaceholder: "ให้รายละเอียดเกี่ยวกับธุรกิจของคุณ, ทรัพย์สิน, รายได้, ฐานผู้ใช้, หรือพอร์ตโฟลิโอของงานที่คุณเสนอสำหรับค่าตอบแทนทางการเงิน",
                monthlyRevenueShareLabel: "ส่วนแบ่งรายได้ต่อเดือนที่ต้องการ (USD) สำหรับโมเดลไฮบริด *",
                monthlyRevenueSharePlaceholder: "เช่น 500",
                equityAcceptanceLabel: "คุณสบายใจกับการชดเชยด้วยส่วนของผู้ถือหุ้นเท่านั้นในเบื้องต้นหรือไม่? *",
                equityYesFull: "ใช่ ฉันสบายใจอย่างเต็มที่กับการชดเชยด้วยส่วนของผู้ถือหุ้นเท่านั้น",
                equityYesConditions: "ใช่ แต่มีเงื่อนไขบางประการ",
                equityHybrid: "ฉันต้องการรูปแบบไฮบริด (เงินเดือนน้อย + ส่วนของผู้ถือหุ้น)",
                equityNo: "ไม่ ฉันต้องการการชำระเงินทันที",
                equityExpectationLabel: "เปอร์เซ็นต์ส่วนของผู้ถือหุ้นเท่าใดที่ดูยุติธรรมสำหรับผลงานของคุณ? *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "เปิดให้มีการเจรจาต่อรองตามมูลค่า",
                timeCommitmentLabel: "คุณสามารถเสนอความมุ่งมั่นด้านเวลาได้เท่าไร? *",
                timePartTime: "พาร์ทไทม์ (10-20 ชั่วโมง/สัปดาห์)",
                timeSubstantial: "มาก (30-40 ชั่วโมง/สัปดาห์)",
                timeFullTime: "มุ่งมั่นเต็มเวลา",
                flexible: "ยืดหยุ่นตามความต้องการ",
                strategicTitle: "คำถามเชิงกลยุทธ์",
                platformToolExpLabel: "ประสบการณ์เกี่ยวกับฟังก์ชันแพลตฟอร์มที่คล้าย Mewayz หรือเครื่องมือที่เกี่ยวข้อง? *",
                platformToolExperiencePlaceholder: "ระบุรายละเอียดประสบการณ์ของคุณกับแพลตฟอร์มเช่น Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite เป็นต้น หรือคุณสมบัติเฉพาะเช่น E-commerce, CRM, Course Platforms, Social Media Management, AI automation, หรือ Escrow Systems",
                creatorEntrepreneurExpLabel: "ประสบการณ์การทำงานร่วมกับ 'ผู้สร้างยุคใหม่' หรือ 'ผู้ประกอบการออนไลน์'? *",
                creatorEntrepreneurExperiencePlaceholder: "อธิบายประสบการณ์ของคุณในการสร้างเนื้อหา, การสอนออนไลน์, การตลาดดิจิทัล, หรือการจัดการธุรกิจออนไลน์",
                mewayzVisionLabel: "วิสัยทัศน์เชิงกลยุทธ์ของคุณสำหรับการเติบโตของ Mewayz? *",
                mewayzGrowthVisionPlaceholder: "พิจารณาคุณสมบัติเฉพาะเช่น ระบบอัตโนมัติที่ขับเคลื่อนด้วย AI, การผสานรวม (Zapier, เกตเวย์การชำระเงิน), หรือการเสริมสร้างชุมชน โปรดกระชับ",
                networkLabel: "อธิบายเครือข่ายมืออาชีพของคุณ *",
                networkPlaceholder: "คุณมีการเชื่อมโยงใดบ้างที่สามารถเป็นประโยชน์ต่อ Mewayz? (นักลงทุน, พันธมิตร, ลูกค้า ฯลฯ)",
                valuePropositionLabel: "คุณจะนำคุณค่าที่เป็นเอกลักษณ์อะไรมาสู่ Mewayz? *",
                valuePropositionPlaceholder: "โปรดระบุให้ชัดเจนว่าอะไรทำให้คุณเป็นพันธมิตรที่เหมาะสม",
                challengesLabel: "คุณเห็นความท้าทายที่ใหญ่ที่สุดสำหรับแพลตฟอร์มธุรกิจแบบครบวงจรอย่าง Mewayz?",
                challengesPlaceholder: "แบ่งปันมุมมองของคุณเกี่ยวกับความท้าทายในอุตสาหกรรมและวิธีที่ Mewayz สามารถเอาชนะได้",
                finalTitle: "การประเมินขั้นสุดท้าย",
                whyNowLabel: "ทำไมคุณถึงสนใจที่จะเข้าร่วม Mewayz ในตอนนี้? *",
                whyNowPlaceholder: "อะไรที่ดึงดูดคุณให้มาสนใจโอกาสนี้ในตอนนี้?",
                financialLabel: "สถานการณ์ทางการเงินปัจจุบัน (การประเมินอย่างตรงไปตรงมา) *",
                financialStable: "มีเสถียรภาพทางการเงิน สามารถทำงานเพื่อส่วนของผู้ถือหุ้นได้",
                financialRunway: "มีเงินทุนสำรองบางส่วน ต้องการรูปแบบไฮบริด",
                financialNeed: "ต้องการรายได้ทันที",
                financialPrivate: "ขอไม่ระบุ",
                availabilityLabel: "คุณสามารถเริ่มงานได้เมื่อไหร่? *",
                availableNow: "ทันที",
                avail1_2weeks: "1-2 สัปดาห์",
                avail1_month: "ภายใน 1 เดือน",
                availableLater: "ต้องการระยะเวลาที่นานขึ้น",
                submitBtn: "ส่งใบสมัคร",
                resultsTitle: "สรุปใบสมัคร",
                nextSteps: "เราจะตรวจสอบใบสมัครของคุณและติดต่อกลับภายใน 48 ชั่วโมงหากมีความเหมาะสม"
            },
            ur: {
                companyIntroTitle: "میویز میں خوش آمدید!",
                companyIntroText: "میویز ایک اختراعی آل ان ون پلیٹ فارم ہے جو جدید تخلیق کاروں اور آن لائن کاروباریوں کو بااختیار بنانے کے لیے ڈیزائن کیا گیا ہے۔ ہم ڈیجیٹل مصنوعات فروخت کرنے، کمیونٹیز بنانے، کورسز بنانے، اور کاروبار کو موثر طریقے سے منظم کرنے کے لیے ٹولز کا ایک جامع سیٹ فراہم کرتے ہیں۔ ہمارا مشن آن لائن کاروبار کو آسان بنانا ہے، تاکہ تخلیق کار اپنی بہترین صلاحیتوں پر توجہ مرکوز کر سکیں: اپنے سامعین کے لیے قدر پیدا کرنا۔",
                infoBoxText: "معلوم نہیں ہم کیا کرتے ہیں؟ یا مزید جاننا چاہتے ہیں؟ <a href='https://mewayz.com'>mewayz.com</a> پر جائیں۔",
                importantNote: "اہم - احتیاط سے پڑھیں",
                warningList: [
                    "یہ ایک ایکویٹی پر مبنی شراکت داری کا موقع ہے (٪ ملکیت)",
                    "میویز کے 30,000+ صارفین ہیں، پروڈکٹ ہنٹ پر #1 تھا، اور اس میں $30,000 کی سرمایہ کاری کی گئی ہے",
                    "ہمارے پاس مارکیٹنگ/ترقی کے لیے $30,000 مختص ہیں",
                    "پچھلی ٹیم کو سابق میٹا/ایپل کے ڈویلپرز سے پیشکشیں موصول ہوئیں (کل 45% ایکویٹی کی درخواست کی گئی تھی)",
                    "اگر آپ فوری تنخواہ/ادائیگی کے خواہاں ہیں تو یہ موقع آپ کے لیے نہیں ہے",
                    "صرف سنجیدہ امیدوار جو ایکویٹی شراکت داری پر یقین رکھتے ہیں، آگے بڑھیں"
                ],
                confirmationTitle: "موقع کی تصدیق",
                confirmLabel: "کیا آپ تصدیق کرتے ہیں کہ یہ میویز کی ایکویٹی شراکت داری کے موقع کے بارے میں ہے؟ *",
                confirmYes: "ہاں، میں سمجھتا ہوں کہ یہ ایکویٹی شراکت داری کے بارے میں ہے",
                confirmNo: "نہیں، مجھے کچھ مختلف توقع تھی",
                personalTitle: "ذاتی معلومات",
                nameLabel: "پورا نام *",
                emailLabel: "ای میل ایڈریس *",
                locationLabel: "مقام (ملک/شہر) *",
                linkedinLabel: "لنکڈ ان پروفائل",
                experienceTitle: "تجربہ اور ہنر",
                roleInterestLabel: "کون سا کردار آپ کو سب سے زیادہ دلچسپی دیتا ہے؟ *",
                productRole: "پروڈکٹ اسٹریٹجی لیڈ",
                partnershipsRole: "اسٹریٹجک شراکت داری",
                devRole: "پروڈکٹ ڈویلپمنٹ / پروگرامنگ",
                socialMediaRole: "سوشل میڈیا اور کمیونٹی مینجمنٹ",
                contentCopyRole: "مواد اور کاپی رائٹنگ لیڈ",
                dataAnalyticsRole: "ڈیٹا اور اینالیٹکس اسپیشلسٹ",
                salesLeadRole: "سیلز لیڈ",
                marketingLeadRole: "مارکیٹنگ لیڈ",
                operationsLeadRole: "آپریشنز لیڈ",
                legalCounselRole: "قانونی مشیر",
                uiUxDesignerRole: "UI/UX ڈیزائنر",
                dataScientistRole: "ڈیٹا سائنٹسٹ",
                customerSuccessRole: "کسٹمر سکسیس لیڈ",
                financeControllerRole: "فنانس کنٹرولر",
                businessAnalystRole: "بزنس اینالسٹ",
                growthLeadRole: "گروتھ لیڈ",
                allRoles: "تمام کردار مجھے دلچسپی دیتے ہیں",
                experienceYearsLabel: "متعلقہ تجربے کے سال؟ *",
                exp0_2: "0-2 سال",
                exp3_5: "3-5 سال",
                exp6_10: "6-10 سال",
                exp10_plus: "10+ سال",
                skillsLabel: "اپنی اہم مہارتیں منتخب کریں (ایک یا زیادہ انتخاب کریں):", 
                skill1: "پروڈکٹ اسٹریٹجی",
                skill2: "بزنس ڈیولپمنٹ",
                skill3: "اسٹریٹجک شراکت داری",
                skill4: "مارکیٹنگ/ترقی",
                skill5: "تکنیکی/ڈویلپمنٹ/پروگرامنگ",
                skill6: "فنڈ ریزنگ",
                skill7: "AI/مشین لرننگ",
                skill8: "ای کامرس پلیٹ فارمز",
                skill9: "CRM سسٹمز",
                skill10: "کمیونٹی بلڈنگ",
                skill11: "کاپی رائٹنگ",
                skill12: "SEO آپٹیمائزیشن",
                skill13: "ویڈیو ایڈیٹنگ",
                skill14: "گرافک ڈیزائن",
                skill15: "ڈیٹا اینالیٹکس",
                skill16: "پراجیکٹ مینجمنٹ",
                skill17: "قانونی و تعمیل",
                skill18: "سیلز اسٹریٹجی",
                skill19: "فنانس و اکاؤٹنگ",
                skill20: "ہیومن ریسورسز",
                companiesLabel: "پچھلی کمپنیاں/قابل ذکر کامیابیاں *",
                previousCompaniesPlaceholder: "ان کمپنیوں کی فہرست بنائیں جہاں آپ نے کام کیا ہے، وہ اسٹارٹ اپ جو آپ نے بنائے ہیں، اہم کامیابیاں، وغیرہ۔",
                compensationTitle: "معاوضہ اور عزم",
                partnershipTypeLabel: "آپ کس قسم کی شراکت داری کی تلاش میں ہیں؟ *",
                partnershipType1: "ایکویٹی پر مبنی شراکت داری",
                partnershipType2: "ہائبرڈ (تنخواہ + ایکویٹی)",
                partnership3: "کاروبار/پورٹ فولیو کے لیے مالی معاوضہ",
                upfrontFeeLabel: "مطلوبہ پیشگی فیس (USD) *",
                upfrontFeePlaceholder: "مثلاً 5000",
                monthlyFeeLabel: "مطلوبہ ماہانہ فیس (USD) *",
                monthlyFeePlaceholder: "مثلاً 1000",
                businessDescriptionLabel: "کاروبار/پورٹ فولیو کی وضاحت کریں جو آپ پیش کر رہے ہیں *",
                businessDescriptionPlaceholder: "اپنے کاروبار، اس کے اثاثوں، آمدنی، صارف بیس، یا کام کے پورٹ فولیو کے بارے میں تفصیلات فراہم کریں جو آپ مالی معاوضے کے لیے پیش کر رہے ہیں۔",
                monthlyRevenueShareLabel: "ہائبرڈ ماڈل کے لیے مطلوبہ ماہانہ ریونیو شیئر (USD) *",
                monthlyRevenueSharePlaceholder: "مثلاً 500",
                equityAcceptanceLabel: "کیا آپ ابتدائی طور پر صرف ایکویٹی پر مبنی معاوضے کے ساتھ آرام دہ ہیں؟ *",
                equityYesFull: "ہاں، میں صرف ایکویٹی پر مبنی معاوضے کے ساتھ مکمل طور پر آرام دہ ہوں",
                equityYesConditions: "ہاں، لیکن کچھ شرائط کے ساتھ",
                equityHybrid: "میں ایک ہائبرڈ ماڈل کو ترجیح دیتا ہوں (چھوٹی تنخواہ + ایکویٹی)",
                equityNo: "نہیں، مجھے فوری ادائیگی کی ضرورت ہے",
                equityExpectationLabel: "آپ کی شراکت کے لیے ایکویٹی کا کتنا فیصد مناسب لگتا ہے؟ *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "قیمت کی بنیاد پر گفت و شنید کے لیے کھلا ہے",
                timeCommitmentLabel: "آپ کتنا وقت دے سکتے ہیں؟ *",
                timePartTime: "پارٹ ٹائم (10-20 گھنٹے/ہفتہ)",
                timeSubstantial: "کافی (30-40 گھنٹے/ہفتہ)",
                timeFullTime: "مکمل وقت عزم",
                flexible: "ضروریات کے مطابق لچکدار",
                strategicTitle: "اسٹریٹجک سوالات",
                platformToolExpLabel: "میویز جیسے پلیٹ فارم کی خصوصیات یا متعلقہ ٹولز کے ساتھ تجربہ؟ *",
                platformToolExperiencePlaceholder: "Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite، وغیرہ جیسے پلیٹ فارمز یا ای کامرس، CRM، کورس پلیٹ فارمز، سوشل میڈیا مینجمنٹ، AI آٹومیشن، یا ایسکرو سسٹمز جیسی مخصوص خصوصیات کے ساتھ اپنے تجربے کی تفصیل بتائیں۔",
                creatorEntrepreneurExpLabel: "’ماڈرن کریئٹرز‘ یا ’آن لائن انٹرپرینورز‘ کے ساتھ کام کرنے کا تجربہ؟ *",
                creatorEntrepreneurExperiencePlaceholder: "مواد کی تخلیق، آن لائن کوچنگ، ڈیجیٹل مارکیٹنگ، یا آن لائن کاروبار کے انتظام میں اپنے تجربے کی وضاحت کریں۔",
                mewayzVisionLabel: "میویز کی ترقی کے لیے آپ کا اسٹریٹجک وژن کیا ہے؟ *",
                mewayzGrowthVisionPlaceholder: "AI-طاقت سے چلنے والی آٹومیشن، انٹیگریشنز (Zapier، ادائیگی کے گیٹ وے)، یا کمیونٹی بلڈنگ کو بہتر بنانے جیسی مخصوص خصوصیات پر غور کریں۔ مختصر رہیں۔",
                networkLabel: "اپنے پیشہ ورانہ نیٹ ورک کی وضاحت کریں *",
                networkPlaceholder: "آپ کے پاس کون سے روابط ہیں جو میویز کو فائدہ پہنچا سکتے ہیں؟ (سرمایہ کار، شراکت دار، کلائنٹس، وغیرہ)",
                valuePropositionLabel: "آپ میویز کو کیا منفرد قدر فراہم کریں گے؟ *",
                valuePropositionPlaceholder: "واضح کریں کہ آپ کو صحیح شراکت دار کیا بناتا ہے",
                challengesLabel: "ایک آل ان ون بزنس پلیٹ فارم جیسے میویز کے لیے آپ کو سب سے بڑے چیلنجز کیا نظر آتے ہیں؟",
                challengesPlaceholder: "صنعتی چیلنجوں پر اپنا نقطہ نظر شیئر کریں اور میویز انہیں کیسے حل کر سکتا ہے۔",
                finalTitle: "حتمی تشخیص",
                whyNowLabel: "آپ اب میویز میں شامل ہونے میں کیوں دلچسپی رکھتے ہیں؟ *",
                whyNowPlaceholder: "اس مرحلے پر آپ کو اس موقع سے کیا چیز متاثر کرتی ہے؟",
                financialLabel: "موجودہ مالی صورتحال (ایماندارانہ تشخیص) *",
                financialStable: "مالی طور پر مستحکم، ایکویٹی کے لیے کام کر سکتا ہے",
                financialRunway: "کچھ مالی وسائل ہیں، ہائبرڈ کو ترجیح دیتا ہوں",
                financialNeed: "فوری آمدنی کی ضرورت ہے",
                financialPrivate: "بتانا پسند نہیں کروں گا",
                availabilityLabel: "آپ کب شروع کر سکتے ہیں؟ *",
                availableNow: "فوری طور پر",
                avail1_2weeks: "1-2 ہفتے",
                avail1_month: "1 ماہ کے اندر",
                availableLater: "زیادہ وقت درکار ہے",
                submitBtn: "درخواست جمع کروائیں",
                resultsTitle: "درخواست کا خلاصہ",
                nextSteps: "ہم آپ کی درخواست کا جائزہ لیں گے اور اگر کوئی ممکنہ مطابقت ہوئی تو 48 گھنٹوں کے اندر آپ سے رابطہ کریں گے۔"
            },
            hi: {
                companyIntroTitle: "मेवेज में आपका स्वागत है!",
                companyIntroText: "मेवेज एक अभिनव ऑल-इन-वन प्लेटफॉर्म है जिसे आधुनिक रचनाकारों और ऑनलाइन उद्यमियों को सशक्त बनाने के लिए डिज़ाइन किया गया है। हम डिजिटल उत्पादों को बेचने, समुदायों का निर्माण करने, पाठ्यक्रम बनाने और व्यवसायों को कुशलतापूर्वक प्रबंधित करने के लिए उपकरणों का एक व्यापक सूट प्रदान करते हैं। हमारा मिशन ऑनलाइन उद्यमिता को सरल बनाना है, जिससे रचनाकारों को वह करने पर ध्यान केंद्रित करने की अनुमति मिलती है जो वे सबसे अच्छा करते हैं: अपने दर्शकों के लिए मूल्य बनाना।",
                infoBoxText: "पता नहीं हम क्या करते हैं? या अधिक जानना चाहते हैं? <a href='https://mewayz.com'>mewayz.com</a> पर जाएं।",
                importantNote: "महत्वपूर्ण - ध्यान से पढ़ें",
                warningList: [
                    "यह एक इक्विटी-आधारित साझेदारी का अवसर है (प्रतिशत स्वामित्व)",
                    "Mewayz के 30,000+ उपयोगकर्ता हैं, प्रोडक्ट हंट पर #1 था, और इसमें $30,000 का निवेश किया गया है",
                    "हमारे पास मार्केटिंग/विकास के लिए $30,000 आवंटित हैं",
                    "पिछली टीम को पूर्व-मेटा/एप्पल डेवलपर्स से प्रस्ताव मिले (कुल 45% इक्विटी का अनुरोध किया गया)",
                    "यदि आप तत्काल वेतन/भुगतान की तलाश में हैं, तो यह अवसर आपके लिए नहीं है",
                    "केवल गंभीर उम्मीदवार जो इक्विटी साझेदारी में विश्वास रखते हैं, आगे बढ़ें"
                ],
                confirmationTitle: "अवसर की पुष्टि",
                confirmLabel: "क्या आप पुष्टि करते हैं कि यह Mewayz इक्विटी साझेदारी के अवसर के बारे में है? *",
                confirmYes: "हां, मैं समझता हूं कि यह इक्विटी साझेदारी के बारे में है",
                confirmNo: "नहीं, मुझे कुछ अलग उम्मीद थी",
                personalTitle: "व्यक्तिगत जानकारी",
                nameLabel: "पूरा नाम *",
                emailLabel: "ईमेल पता *",
                locationLabel: "स्थान (देश/शहर) *",
                linkedinLabel: "लिंक्डइन प्रोफाइल",
                experienceTitle: "अनुभव और कौशल",
                roleInterestLabel: "आपको कौन सी भूमिका सबसे ज्यादा पसंद है? *",
                productRole: "उत्पाद रणनीति लीड",
                partnershipsRole: "रणनीतिक साझेदारी",
                devRole: "उत्पाद विकास / प्रोग्रामिंग",
                socialMediaRole: "सोशल मीडिया और सामुदायिक प्रबंधन",
                contentCopyRole: "सामग्री और कॉपी राइटिंग लीड",
                dataAnalyticsRole: "डेटा और एनालिटिक्स विशेषज्ञ",
                salesLeadRole: "सेल्स लीड",
                marketingLeadRole: "मार्केटिंग लीड",
                operationsLeadRole: "ऑपरेशंस लीड",
                legalCounselRole: "कानूनी सलाहकार",
                uiUxDesignerRole: "UI/UX डिज़ाइनर",
                dataScientistRole: "डेटा साइंटिस्ट",
                customerSuccessRole: "कस्टमर सक्सेस लीड",
                financeControllerRole: "फाइनेंस कंट्रोलर",
                businessAnalystRole: "बिज़नेस एनालिस्ट",
                growthLeadRole: "ग्रोथ लीड",
                allRoles: "सभी भूमिकाएं मुझे पसंद हैं",
                experienceYearsLabel: "प्रासंगिक अनुभव के वर्ष? *",
                exp0_2: "0-2 वर्ष",
                exp3_5: "3-5 वर्ष",
                exp6_10: "6-10 वर्ष",
                exp10_plus: "10+ वर्ष",
                skillsLabel: "अपने मुख्य कौशल चुनें (एक या अधिक चुनें):", 
                skill1: "उत्पाद रणनीति",
                skill2: "व्यवसाय विकास",
                skill3: "रणनीतिक साझेदारी",
                skill4: "विपणन/विकास",
                skill5: "तकनीकी/विकास/प्रोग्रामिंग",
                skill6: "धन उगाही",
                skill7: "एआई/मशीन लर्निंग",
                skill8: "ई-कॉमर्स प्लेटफॉर्म",
                skill9: "सीआरएम सिस्टम",
                skill10: "सामुदायिक निर्माण",
                skill11: "कॉपी राइटिंग",
                skill12: "एसईओ अनुकूलन",
                skill13: "वीडियो संपादन",
                skill14: "ग्राफिक डिजाइन",
                skill15: "डेटा विश्लेषण",
                skill16: "परियोजना प्रबंधन",
                skill17: "कानूनी और अनुपालन",
                skill18: "सेल्स रणनीति",
                skill19: "वित्त और लेखा",
                skill20: "मानव संसाधन",
                companiesLabel: "पिछली कंपनियां/उल्लेखनीय उपलब्धियां *",
                previousCompaniesPlaceholder: "उन कंपनियों को सूचीबद्ध करें जिनके लिए आपने काम किया है, आपके द्वारा बनाए गए स्टार्टअप, प्रमुख उपलब्धियां आदि।",
                compensationTitle: "मुआवजा और प्रतिबद्धता",
                partnershipTypeLabel: "आप किस प्रकार की साझेदारी की तलाश में हैं? *",
                partnershipType1: "इक्विटी-आधारित साझेदारी",
                partnershipType2: "हाइब्रिड (वेतन + इक्विटी)",
                partnershipType3: "व्यवसाय/पोर्टफोलियो के लिए मौद्रिक मुआवजा",
                upfrontFeeLabel: "वांछित अग्रिम शुल्क (USD) *",
                upfrontFeePlaceholder: "उदाहरण के लिए, 5000",
                monthlyFeeLabel: "वांछित मासिक शुल्क (USD) *",
                monthlyFeePlaceholder: "उदाहरण के लिए, 1000",
                businessDescriptionLabel: "आप जिस व्यवसाय/पोर्टफोलियो की पेशकश कर रहे हैं उसका वर्णन करें *",
                businessDescriptionPlaceholder: "अपने व्यवसाय, उसकी संपत्ति, राजस्व, उपयोगकर्ता आधार, या काम के पोर्टफोलियो के बारे में विवरण प्रदान करें जो आप मौद्रिक मुआवजे के लिए पेशकश कर रहे हैं۔",
                monthlyRevenueShareLabel: "हाइब्रिड मॉडल के लिए वांछित मासिक राजस्व शेयर (USD) *",
                monthlyRevenueSharePlaceholder: "उदाहरण के लिए, 500",
                equityAcceptanceLabel: "क्या आप शुरू में केवल इक्विटी-आधारित मुआवजे के साथ सहज हैं? *",
                equityYesFull: "हां, मैं केवल इक्विटी-आधारित मुआवजे के साथ पूरी तरह सहज हूं",
                equityYesConditions: "हां, लेकिन कुछ शर्तों के साथ",
                equityHybrid: "मैं एक हाइब्रिड माडل को तरजीح دیتا ہوں (چھوٹی تنخواہ + ایکویٹی)",
                equityNo: "नहीं، مجھے فوری ادائیگی کی ضرورت ہے",
                equityExpectationLabel: "आप की شراکت کے لیے ایکویٹی کا کتنا فیصد مناسب لگتا ہے؟ *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "कीमत की بنیاد پر گفت و شنید کے لیے کھلا ہے",
                timeCommitmentLabel: "आप कतना वक्त दे सकते हैं? *",
                timePartTime: "पार्ट टाइम (10-20 घंटे/हफ्ता)",
                timeSubstantial: "काफी (30-40 घंटे/हफ्ता)",
                timeFullTime: "फुल टाइम कमिटमेंट",
                flexible: "जरूरतों के मुताबिक लचीला",
                strategicTitle: "रणनीतिक सवाल",
                platformToolExpLabel: "मेवेज जैसे प्लेटफॉर्म की विशेषताओं या संबंधित उपकरणों के साथ अनुभव? *",
                platformToolExperiencePlaceholder: "एआई-संचालित स्वचालन, एकीकरण (जैपियर, भुगतान गेटवे), या समुदाय निर्माण को बढ़ाने जैसी विशिष्ट विशेषताओं पर विचार करें। संक्षिप्त रहें।",
                creatorEntrepreneurExpLabel: "Experience working with 'Modern Creators' or 'Online Entrepreneurs'? *",
                creatorEntrepreneurExperiencePlaceholder: "सामग्री निर्माण, ऑनलाइन कोचिंग, डिजिटल मार्केटिंग, या ऑनलाइन कारोबार के प्रबंधन में अपने अनुभव की व्याख्या करें۔",
                mewayzVisionLabel: "Your strategic vision for Mewayz's growth? *",
                mewayzGrowthVisionPlaceholder: "एआई-संचालित स्वचालन, एकीकरण (जैपियर, भुगतान गेटवे), या समुदाय निर्माण को बढ़ाने जैसी विशिष्ट विशेषताओं पर विचार करें। संक्षिप्त रहें۔",
                networkLabel: "Describe your professional network *",
                networkPlaceholder: "आपके पास कौन से कनेक्शन हैं जो मेवेज को फायदा पहुंचा सकते हैं? (सर्मयाकार, साझेदार, क्लाइंट्स, वगैरह)",
                valuePropositionLabel: "What unique value would you bring to Mewayz? *",
                valuePropositionPlaceholder: "विशिष्ट रहें कि आपको सही भागीदार क्या बनाता है",
                challengesLabel: "What do you see as the biggest challenges for an all-in-one business platform like Mewayz?",
                challengesPlaceholder: "उद्योग की चुनौतियों पर अपना दृष्टिकोण साझा करें और मेवेज उन्हें कैसे हल कर सकता है?",
                finalTitle: "अंतिम मूल्यांकन",
                whyNowLabel: "Why are you interested in joining Mewayz now? *",
                whyNowPlaceholder: "इस स्तर पर آپ کو اس موقع से क्या चीज متاثر کرتی ہے؟",
                financialLabel: "Current financial situation (honest assessment) *",
                financialStable: "مالی طور پر مستحکم، एकویटी के लिए काम कर सकता है",
                financialRunway: "कुछ वित्तीय रनवे है, हाइब्रिड पसंद करता है",
                financialNeed: "तत्काल आय की आवश्यकता है",
                financialPrivate: "बताना पसंद नहीं करोंगा",
                availabilityLabel: "When could you start? *",
                availableNow: "तुरंत",
                avail1_2weeks: "1-2 सप्ताह",
                avail1_month: "1 महीने के भीतर",
                availableLater: "लंबा समय चाहिए",
                submitBtn: "درخواست جمع کریں",
                resultsTitle: "درخواست کا खुलासा",
                nextSteps: "हम आपकी दरख्वास्त की समीक्षा करेंगे और अगर कोई ممکنہ मेल होता है तो 48 घंटों के अंदर आपसे संपर्क करेंगे।"
            },
            es: {
                companyIntroTitle: "¡Bienvenido a Mewayz!",
                companyIntroText: "Mewayz es una plataforma todo en uno innovadora diseñada para empoderar a los creadores modernos y emprendedores en línea. Ofrecemos un conjunto completo de herramientas para vender productos digitales, construir comunidades, crear cursos y gestionar negocios de manera eficiente. Nuestra misión es simplificar el emprendimiento en línea, permitiendo a los creadores centrarse en lo que mejor saben hacer: crear valor para su audiencia.",
                infoBoxText: "¿No sabe qué hacemos? ¿O quiere saber más? Vaya a <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "IMPORTANTE - LEA ATENTAMENTE",
                warningList: [
                    "Esta es una oportunidad de asociación BASADA EN CAPITAL (porcentaje de propiedad)",
                    "Mewayz tiene más de 30,000 usuarios, fue #1 en Product Hunt y ha invertido $30,000",
                    "Hemos asignado $30,000 para marketing/crecimiento",
                    "El equipo anterior recibió ofertas de desarrolladores ex-Meta/Apple (45% de capital total solicitado)",
                    "Si busca un salario/pago inmediato, esta oportunidad NO es para usted",
                    "Solo los candidatos serios que crean en las asociaciones de capital deben continuar"
                ],
                confirmationTitle: "Confirmación de Oportunidad",
                confirmLabel: "¿Confirma que se trata de la oportunidad de asociación de capital de Mewayz? *",
                confirmYes: "Sí, entiendo que se trata de una asociación de capital",
                confirmNo: "No, esperaba algo diferente",
                personalTitle: "Información Personal",
                nameLabel: "Nombre Completo *",
                emailLabel: "Dirección de Correo Electrónico *",
                locationLabel: "Ubicación (País/Ciudad) *",
                linkedinLabel: "Perfil de LinkedIn",
                experienceTitle: "Experiencia y Habilidades",
                roleInterestLabel: "¿Qué rol le interesa más? *",
                productRole: "Líder de Estrategia de Producto",
                partnershipsRole: "Asociaciones Estratégicas",
                devRole: "Desarrollo de Producto / Programación",
                socialMediaRole: "Gestión de Redes Sociales y Comunidad",
                contentCopyRole: "Líder de Contenido y Copywriting",
                dataAnalyticsRole: "Especialista en Datos y Análisis",
                salesLeadRole: "Líder de Ventas",
                marketingLeadRole: "Líder de Marketing",
                operationsLeadRole: "Líder de Operaciones",
                legalCounselRole: "Asesor Legal",
                uiUxDesignerRole: "Diseñador UI/UX",
                dataScientistRole: "Científico de Datos",
                customerSuccessRole: "Líder de Éxito del Cliente",
                financeControllerRole: "Controlador Financiero",
                businessAnalystRole: "Analista de Negocios",
                growthLeadRole: "Líder de Crecimiento",
                allRoles: "Todos los roles me interesan",
                experienceYearsLabel: "¿Años de experiencia relevante? *",
                exp0_2: "0-2 años",
                exp3_5: "3-5 años",
                exp6_10: "6-10 años",
                exp10_plus: "Más de 10 años",
                skillsLabel: "Seleccione sus habilidades clave (seleccione una o más):", 
                skill1: "Estrategia de Producto",
                skill2: "Desarrollo de Negocios",
                skill3: "Asociaciones Estratégicas",
                skill4: "Marketing/Crecimiento",
                skill5: "Técnico/Desarrollo/Programación",
                skill6: "Recaudación de Fondos",
                skill7: "IA/Aprendizaje Automático",
                skill8: "Plataformas de Comercio Electrónico",
                skill9: "Sistemas CRM",
                skill10: "Construcción de Comunidad",
                skill11: "Copywriting",
                skill12: "Optimización SEO",
                skill13: "Edición de Video",
                skill14: "Diseño Gráfico",
                skill15: "Análisis de Datos",
                skill16: "Gestión de Proyectos",
                skill17: "Legal y Cumplimiento",
                skill18: "Estrategia de Ventas",
                skill19: "Finanzas y Contabilidad",
                skill20: "Recursos Humanos",
                companiesLabel: "Empresas anteriores/logros notables *",
                previousCompaniesPlaceholder: "Liste las empresas en las que ha trabajado, las startups que ha creado, los principales logros, etc.",
                compensationTitle: "Compensación y Compromiso",
                partnershipTypeLabel: "¿Qué tipo de asociación busca? *",
                partnershipType1: "Asociación Basada en Capital",
                partnershipType2: "Híbrido (Salario + Capital)",
                partnershipType3: "Compensación Monetaria por Negocio/Portafolio",
                upfrontFeeLabel: "Tarifa Inicial Deseada (USD) *",
                upfrontFeePlaceholder: "ej. 5000",
                monthlyFeeLabel: "Tarifa Mensual Deseada (USD) *",
                monthlyFeePlaceholder: "ej. 1000",
                businessDescriptionLabel: "Describa el Negocio/Portafolio que está ofreciendo *",
                businessDescriptionPlaceholder: "Proporcione detalles sobre su negocio, sus activos, ingresos, base de usuarios o la cartera de trabajo que está ofreciendo a cambio de compensación monetaria.",
                monthlyRevenueShareLabel: "Participación Mensual en los Ingresos Deseada (USD) para Modelo Híbrido *",
                monthlyRevenueSharePlaceholder: "ej. 500",
                equityAcceptanceLabel: "¿Se siente cómodo con una compensación inicial solo con capital? *",
                equityYesFull: "Sí, me siento completamente cómodo con solo capital",
                equityYesConditions: "Sí, pero con algunas condiciones",
                equityHybrid: "Prefiero un modelo híbrido (pequeño salario + capital)",
                equityNo: "No, necesito pago inmediato",
                equityExpectationLabel: "¿Qué porcentaje de capital le parece justo para su contribución? *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "Más del 25%",
                negotiable: "Abierto a negociación según el valor",
                timeCommitmentLabel: "¿Compromiso de tiempo que puede ofrecer? *",
                timePartTime: "Medio tiempo (10-20 horas/semana)",
                timeSubstantial: "Sustancial (30-40 horas/semana)",
                timeFullTime: "Compromiso a tiempo completo",
                flexible: "Flexible según las necesidades",
                strategicTitle: "Preguntas Estratégicas",
                platformToolExpLabel: "Experiencia con características de plataforma similares a Mewayz o herramientas relevantes? *",
                platformToolExperiencePlaceholder: "Detalle su experiencia con plataformas como Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite, etc., o características específicas como comercio electrónico, CRM, plataformas de cursos, gestión de redes sociales, automatización de IA o sistemas de custodia.",
                creatorEntrepreneurExpLabel: "Experiencia trabajando con 'Creadores Modernos' o 'Emprendedores Online'? *",
                creatorEntrepreneurExperiencePlaceholder: "Describa su experiencia en creación de contenido, coaching online, marketing digital o gestión de un negocio online.",
                mewayzVisionLabel: "Su visión estratégica para el crecimiento de Mewayz? *",
                mewayzGrowthVisionPlaceholder: "Considere características específicas como la automatización impulsada por IA, integraciones (Zapier, pasarelas de pago) o la mejora de la construcción de la comunidad. Sea conciso.",
                networkLabel: "Describa su red profesional *",
                networkPlaceholder: "Qué conexiones tiene que podrían beneficiar a Mewayz? (inversores, socios, clientes, etc.)",
                valuePropositionLabel: "Qué valor único aportaría a Mewayz? *",
                valuePropositionPlaceholder: "Sea específico sobre lo que lo convierte en el socio adecuado",
                challengesLabel: "Qué desafíos ve como los más grandes para una plataforma de negocios todo en uno como Mewayz?",
                challengesPlaceholder: "Comparta su perspectiva sobre los desafíos de la industria y cómo Mewayz puede superarlos.",
                finalTitle: "Evaluación Final",
                whyNowLabel: "Por qué le interesa unirse a Mewayz ahora? *",
                whyNowPlaceholder: "Qué le atrae de esta oportunidad en esta etapa?",
                financialLabel: "Situación financiera actual (evaluación honesta) *",
                financialStable: "Financieramente estable, puede trabajar por capital",
                financialRunway: "Tengo algo de margen, prefiero híbrido",
                financialNeed: "Necesito ingresos inmediatos",
                financialPrivate: "Prefiero no decirlo",
                availabilityLabel: "Cuándo podría empezar? *",
                availableNow: "Inmediatamente",
                avail1_2weeks: "1-2 semanas",
                avail1_month: "Dentro de 1 mes",
                availableLater: "Se necesita más tiempo",
                submitBtn: "Enviar Solicitud",
                resultsTitle: "Resumen de la Solicitud",
                nextSteps: "Revisaremos su solicitud y nos pondremos en contacto con usted dentro de las 48 horas si hay una posible coincidencia."
            },
            fr: {
                companyIntroTitle: "Bienvenue chez Mewayz!",
                companyIntroText: "Mewayz est une plateforme tout-en-un innovante conçue pour autonomiser les créateurs modernes et les entrepreneurs en ligne. Nous fournissons une suite complète d'outils pour vendre des produits numériques, construire des communautés, créer des cours et gérer efficacement les entreprises. Notre mission est de simplifier l'entrepreneuriat en ligne, permettant aux créateurs de se concentrer sur ce qu'ils font de mieux : créer de la valeur pour leur public.",
                infoBoxText: "Vous ne savez pas ce que nous faisons ? Ou vous voulez en savoir plus ? Allez sur <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "IMPORTANT - LIRE ATTENTIVEMENT",
                warningList: [
                    "Ceci est une opportunité de partenariat BASÉE SUR L'ÉQUITÉ (pourcentage de propriété)",
                    "Mewayz a plus de 30 000 utilisateurs, a été n°1 sur Product Hunt et a investi 30 000 $",
                    "Nous avons alloué 30 000 $ pour le marketing/la croissance",
                    "L'équipe précédente a reçu des offres d'anciens développeurs Meta/Apple (45 % du capital total demandé)",
                    "Si vous recherchez un salaire/paiement immédiat, cette opportunité N'EST PAS pour vous",
                    "Seuls les candidats sérieux qui croient aux partenariats en capitaux propres doivent poursuivre"
                ],
                confirmationTitle: "Confirmation d'Opportunité",
                confirmLabel: "Confirmez-vous que cela concerne l'opportunité de partenariat en capitaux propres Mewayz? *",
                confirmYes: "Oui, je comprends qu'il s'agit d'un partenariat en capitaux propres",
                confirmNo: "Non, j'attendais quelque chose de différent",
                personalTitle: "Informations Personnelles",
                nameLabel: "Nom Complet *",
                emailLabel: "Adresse E-mail *",
                locationLabel: "Localisation (Pays/Ville) *",
                linkedinLabel: "Profil LinkedIn",
                experienceTitle: "Expérience & Compétences",
                roleInterestLabel: "Quel rôle vous intéresse le plus? *",
                productRole: "Responsable Stratégie Produit",
                partnershipsRole: "Partenariats Stratégiques",
                devRole: "Développement de Produit / Programmation",
                socialMediaRole: "Gestion des Médias Sociaux et de la Communauté",
                contentCopyRole: "Responsable Contenu et Rédaction",
                dataAnalyticsRole: "Spécialiste Données et Analyse",
                salesLeadRole: "Responsable des Ventes",
                marketingLeadRole: "Responsable Marketing",
                operationsLeadRole: "Responsable des Opérations",
                legalCounselRole: "Conseiller Juridique",
                uiUxDesignerRole: "Designer UI/UX",
                dataScientistRole: "Scientifique des Données",
                customerSuccessRole: "Responsable du Succès Client",
                financeControllerRole: "Contrôleur Financier",
                businessAnalystRole: "Analyste Commercial",
                growthLeadRole: "Responsable de la Croissance",
                allRoles: "Tous les rôles m'intéressent",
                experienceYearsLabel: "Années d'expérience pertinente? *",
                exp0_2: "0-2 ans",
                exp3_5: "3-5 ans",
                exp6_10: "6-10 ans",
                exp10_plus: "10+ ans",
                skillsLabel: "Sélectionnez vos compétences clés (sélectionnez une ou plus):", 
                skill1: "Stratégie Produit",
                skill2: "Développement Commercial",
                skill3: "Partenariats Stratégiques",
                skill4: "Marketing/Croissance",
                skill5: "Technique/Développement/Programmation",
                skill6: "Levée de Fonds",
                skill7: "IA/Apprentissage Automatique",
                skill8: "Plateformes E-commerce",
                skill9: "Systèmes CRM",
                skill10: "Création de Communauté",
                skill11: "Rédaction (Copywriting)",
                skill12: "Optimisation SEO",
                skill13: "Montage Vidéo",
                skill14: "Conception Graphique",
                skill15: "Analyse de Données",
                skill16: "Gestion de Projet",
                skill17: "Juridique et Conformité",
                skill18: "Stratégie de Vente",
                skill19: "Finance et Comptabilité",
                skill20: "Ressources Humaines",
                companiesLabel: "Entreprises précédentes/réalisations notables *",
                previousCompaniesPlaceholder: "Listez les entreprises pour lesquelles vous avez travaillé, les startups que vous avez créées, les réalisations majeures, etc.",
                compensationTitle: "Rémunération & Engagement",
                partnershipTypeLabel: "Quel type de partenariat recherchez-vous? *",
                partnershipType1: "Partenariat Basé sur l'Équité",
                partnershipType2: "Hybride (Salaire + Équité)",
                partnershipType3: "Compensation Monétaire pour Affaire/Portefeuille",
                upfrontFeeLabel: "Frais Initiaux Désirés (USD) *",
                upfrontFeePlaceholder: "ex. 5000",
                monthlyFeeLabel: "Frais Mensuels Désirés (USD) *",
                monthlyFeePlaceholder: "ex. 1000",
                businessDescriptionLabel: "Décrivez l'Affaire/Portefeuille que vous proposez *",
                businessDescriptionPlaceholder: "Fournissez des détails sur votre entreprise, ses actifs, ses revenus, sa base d'utilisateurs ou le portefeuille de travaux que vous proposez en échange d'une compensation monétaire.",
                monthlyRevenueShareLabel: "Partage de Revenus Mensuel Désiré (USD) pour Modèle Hybride *",
                monthlyRevenueSharePlaceholder: "ex. 500",
                equityAcceptanceLabel: "Êtes-vous à l'aise avec une rémunération initialement basée uniquement sur l'équité? *",
                equityYesFull: "Oui, je suis entièrement à l'aise avec une rémunération uniquement en équité",
                equityYesConditions: "Oui, mais avec certaines conditions",
                equityHybrid: "Je préfère un modèle hybride (petit salaire + équité)",
                equityNo: "Non, j'ai besoin d'un paiement immédiat",
                equityExpectationLabel: "Quel pourcentage d'équité semble juste pour votre contribution? *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "Ouvert à la négociation basée sur la valeur",
                timeCommitmentLabel: "Engagement de temps que vous pouvez offrir? *",
                timePartTime: "Temps partiel (10-20 heures/semaine)",
                timeSubstantial: "Substantiel (30-40 heures/semaine)",
                timeFullTime: "Engagement à temps plein",
                flexible: "Flexible selon les besoins",
                strategicTitle: "Questions Stratégiques",
                platformToolExpLabel: "Expérience avec des fonctionnalités de plateforme similaires à Mewayz ou des outils pertinents? *",
                platformToolExperiencePlaceholder: "Détaillez votre expérience avec des plateformes comme Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite, etc., o.u des fonctionnalités spécifiques como el comercio electrónico, el CRM, las plataformas de cursos, la gestión de redes sociales, la automatización de IA o los sistemas de séquestre.",
                creatorEntrepreneurExpLabel: "Expérience de travail avec des 'Créateurs Modernes' ou des 'Entrepreneurs en Ligne'? *",
                creatorEntrepreneurExperiencePlaceholder: "Décrivez votre expérience en création de contenu, coaching en ligne, marketing numérique ou gestion d'une entreprise en ligne.",
                mewayzVisionLabel: "Votre vision stratégique pour la croissance de Mewayz? *",
                mewayzGrowthVisionPlaceholder: "Considérez des fonctionnalités spécifiques telles que l'automatisation alimentée par l'IA, les intégrations (Zapier, passerelles de paiement) ou l'amélioration de la création de communauté. Soyez concis.",
                networkLabel: "Décrivez votre réseau professionnel *",
                networkPlaceholder: "Quelles connexions avez-vous qui pourraient bénéficier à Mewayz? (investisseurs, partenaires, clients, etc.)",
                valuePropositionLabel: "Quelle valeur unique apporteriez-vous à Mewayz? *",
                valuePropositionPlaceholder: "Soyez précis sur ce qui fait de vous le bon partenaire",
                challengesLabel: "Quels sont les plus grands défis pour une plateforme d'entreprise tout-en-un comme Mewayz?",
                challengesPlaceholder: "Partagez votre perspective sur les défis de l'industrie et comment Mewayz peut les surmonter.",
                finalTitle: "Évaluation Finale",
                whyNowLabel: "Pourquoi êtes-vous intéressé à rejoindre Mewayz maintenant? *",
                whyNowPlaceholder: "Qu'est-ce qui vous attire dans cette opportunité à ce stade?",
                financialLabel: "Situation financière actuelle (évaluation honnête) *",
                financialStable: "Financièrement stable, peut travailler pour l'équité",
                financialRunway: "A un certain fonds de roulement, préfère l'hybride",
                financialNeed: "Besoin de revenus immédiats",
                financialPrivate: "Préfère ne pas dire",
                availabilityLabel: "Quand pourriez-vous commencer? *",
                availableNow: "Immédiatement",
                avail1_2weeks: "1-2 semaines",
                avail1_month: "Dans 1 mois",
                availableLater: "Délai plus long nécessaire",
                submitBtn: "Soumettre l'Application",
                resultsTitle: "Résumé de l'Application",
                nextSteps: "Nous examinerons votre candidature et vous recontacterons dans les 48 heures si un potentiel correspond."
            },
            de: {
                companyIntroTitle: "Willkommen bei Mewayz!",
                companyIntroText: "Mewayz ist eine innovative All-in-One-Plattform, die darauf ausgelegt ist, moderne Kreative und Online-Unternehmer zu befähigen. Wir bieten eine umfassende Suite von Tools zum Verkauf digitaler Produkte, zum Aufbau von Gemeinschaften, zur Erstellung von Kursen und zur effizienten Verwaltung von Unternehmen. Unsere Mission ist es, Online-Unternehmertum zu vereinfachen, damit sich Kreative auf das konzentrieren können, was sie am besten können: Wert für ihr Publikum schaffen.",
                infoBoxText: "Sie wissen nicht, was wir tun? Oder möchten Sie mehr erfahren? Gehen Sie zu <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "WICHTIG - SORGFÄLTIG LESEN",
                warningList: [
                    "Dies ist eine EQUITY-BASIERTE Partnerschaftsmöglichkeit (% Eigentum)",
                    "Mewayz hat über 30.000 Benutzer, war #1 auf Product Hunt und hat $30.000 investiert",
                    "Wir haben $30.000 für Marketing/Wachstum bereitgestellt",
                    "Das frühere Team erhielt Angebote von ehemaligen Meta/Apple-Entwicklern (insgesamt 45 % Eigenkapital angefordert)",
                    "Wenn Sie ein sofortiges Gehalt/Zahlung suchen, ist diese Gelegenheit NICHT für Sie",
                    "Nur ernsthafte Kandidaten, die an Equity-Partnerschaften glauben, sollten fortfahren"
                ],
                confirmationTitle: "Gelegenheitsbestätigung",
                confirmLabel: "Bestätigen Sie, dass es sich um die Mewayz Equity-Partnerschaftsmöglichkeit handelt? *",
                confirmYes: "Ja, ich verstehe, dass es sich um eine Equity-Partnerschaft handelt",
                confirmNo: "Nein, ich hatte etwas anderes erwartet",
                personalTitle: "Persönliche Informationen",
                nameLabel: "Vollständiger Name *",
                emailLabel: "E-Mail-Adresse *",
                locationLabel: "Standort (Land/Stadt) *",
                linkedinLabel: "LinkedIn-Profil",
                experienceTitle: "Erfahrung & Fähigkeiten",
                roleInterestLabel: "Welche Rolle interessiert Sie am meisten? *",
                productRole: "Leiter Produktstrategie",
                partnershipsRole: "Strategische Partnerschaften",
                devRole: "Produktentwicklung / Programmierung",
                socialMediaRole: "Social Media & Community Management",
                contentCopyRole: "Content & Copywriting Leiter",
                dataAnalyticsRole: "Daten- & Analysespecialist",
                salesLeadRole: "Verkaufsleiter",
                marketingLeadRole: "Marketingleiter",
                operationsLeadRole: "Betriebsleiter",
                legalCounselRole: "Rechtsberater",
                uiUxDesignerRole: "UI/UX Designer",
                dataScientistRole: "Datenwissenschaftler",
                customerSuccessRole: "Leiter Kundenerfolg",
                financeControllerRole: "Finanzcontroller",
                businessAnalystRole: "Business Analyst",
                growthLeadRole: "Wachstumsleiter",
                allRoles: "Alle Rollen interessieren mich",
                experienceYearsLabel: "Jahre relevanter Erfahrung? *",
                exp0_2: "0-2 Jahre",
                exp3_5: "3-5 Jahre",
                exp6_10: "6-10 Jahre",
                exp10_plus: "10+ Jahre",
                skillsLabel: "Wählen Sie Ihre Schlüsselkompetenzen aus (eine oder mehr):", 
                skill1: "Produktstrategie",
                skill2: "Geschäftsentwicklung",
                skill3: "Strategische Partnerschaften",
                skill4: "Marketing/Wachstum",
                skill5: "Technisch/Entwicklung/Programmierer",
                skill6: "Fundraising",
                skill7: "KI/Maschinelles Lernen",
                skill8: "E-Commerce-Plattformen",
                skill9: "CRM-Systeme",
                skill10: "Community Building",
                skill11: "Texten (Copywriting)",
                skill12: "SEO-Optimierung",
                skill13: "Videobearbeitung",
                skill14: "Grafikdesign",
                skill15: "Datenanalyse",
                skill16: "Projektmanagement",
                skill17: "Recht & Compliance",
                skill18: "Vertriebsstrategie",
                skill19: "Finanzen & Buchhaltung",
                skill20: "Personalwesen",
                companiesLabel: "Frühere Unternehmen/bemerkenswerte Erfolge *",
                previousCompaniesPlaceholder: "Listen Sie Unternehmen auf, für die Sie gearbeitet haben, Startups, die Sie aufgebaut haben, wichtige Erfolge usw.",
                compensationTitle: "Vergütung & Engagement",
                partnershipTypeLabel: "Welche Art von Partnerschaft suchen Sie? *",
                partnershipType1: "Equity-basierte Partnerschaft",
                partnershipType2: "Hybrid (Gehalt + Equity)",
                partnershipType3: "Monetäre Vergütung für Business/Portfolio",
                upfrontFeeLabel: "Gewünschte Vorauszahlung (USD) *",
                upfrontFeePlaceholder: "z.B. 5000",
                monthlyFeeLabel: "Gewünschte monatliche Gebühr (USD) *",
                monthlyFeePlaceholder: "z.B. 1000",
                businessDescriptionLabel: "Beschreiben Sie das Business/Portfolio, das Sie anbieten *",
                businessDescriptionPlaceholder: "Geben Sie Details zu Ihrem Unternehmen, seinen Vermögenswerten, Einnahmen, der Nutzerbasis oder dem Portfolio an Arbeiten an, die Sie gegen monetäre Vergütung anbieten.",
                monthlyRevenueShareLabel: "Gewünschter monatlicher Umsatzanteil (USD) für Hybridmodell *",
                monthlyRevenueSharePlaceholder: "z.B. 500",
                equityAcceptanceLabel: "Sind Sie anfangs mit einer reinen Eigenkapitalvergütung einverstanden? *",
                equityYesFull: "Ja, ich bin vollkommen einverstanden mit reiner Eigenkapitalvergütung",
                equityYesConditions: "Ja, aber mit einigen Bedingungen",
                equityHybrid: "Ich bevorzuge ein Hybridmodell (kleines Gehalt + Eigenkapital)",
                equityNo: "Nein, ich benötige sofortige Zahlung",
                equityExpectationLabel: "Welcher Eigenkapitalprozentsatz erscheint Ihnen für Ihren Beitrag fair? *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "Verhandlungsbereit basierend auf Wert",
                timeCommitmentLabel: "Zeitlicher Einsatz, den Sie anbieten können? *",
                timePartTime: "Teilzeit (10-20 Stunden/Woche)",
                timeSubstantial: "Erheblich (30-40 Stunden/Woche)",
                timeFullTime: "Vollzeitengagement",
                flexible: "Flexibel je nach Bedarf",
                strategicTitle: "Strategische Fragen",
                platformToolExpLabel: "Erfahrung mit Mewayz-ähnlichen Plattformfunktionen oder relevanten Tools? *",
                platformToolExperiencePlaceholder: "Beschreiben Sie Ihre Erfahrungen mit Plattformen wie Shopify, Teachable, ActiveCampaign, HubSpot, Hootsuite usw. oder spezifischen Funktionen wie E-Commerce, CRM, Kursplattformen, Social Media Management, KI-Automatisierung oder Treuhand-Systemen.",
                creatorEntrepreneurExpLabel: "Erfahrung in der Zusammenarbeit mit 'Modernen Kreatoren' oder 'Online-Unternehmern'? *",
                creatorEntrepreneurExperiencePlaceholder: "Beschreiben Sie Ihre Erfahrungen in der Inhaltserstellung, im Online-Coaching, im digitalen Marketing oder in der Verwaltung eines Online-Geschäfts.",
                mewayzVisionLabel: "Ihre strategische Vision für das Wachstum von Mewayz? *",
                mewayzGrowthVisionPlaceholder: "Berücksichtigen Sie spezifische Funktionen wie KI-gesteuerte Automatisierung, Integrationen (Zapier, Zahlungsgateways) oder die Verbesserung des Community Buildings. Fassen Sie sich kurz.",
                networkLabel: "Beschreiben Sie Ihr berufliches Netzwerk *",
                networkPlaceholder: "Welche Kontakte haben Sie, die Mewayz zugute kommen könnten? (Investoren, Partner, Kunden usw.)",
                valuePropositionLabel: "Welchen einzigartigen Wert würden Sie Mewayz bringen? *",
                valuePropositionPlaceholder: "Seien Sie spezifisch, was Sie zum richtigen Partner macht",
                challengesLabel: "Was sehen Sie als die größten Herausforderungen für eine All-in-One-Business-Plattform wie Mewayz?",
                challengesPlaceholder: "Teilen Sie Ihre Perspektive zu Branchenherausforderungen und wie Mewayz diese bewältigen kann.",
                finalTitle: "Abschließende Beurteilung",
                whyNowLabel: "Warum sind Sie jetzt daran interessiert, Mewayz beizutreten? *",
                whyNowPlaceholder: "Was reizt Sie an dieser Gelegenheit in diesem Stadium?",
                financialLabel: "Aktuelle finanzielle Situation (ehrliche Einschätzung) *",
                financialStable: "Finanziell stabil, kann für Eigenkapital arbeiten",
                financialRunway: "Habe etwas finanzielle Spielraum, bevorzuge Hybrid",
                financialNeed: "Benötige sofortiges Einkommen",
                financialPrivate: "Möchte ich nicht sagen",
                availabilityLabel: "Wann könnten Sie anfangen? *",
                availableNow: "Sofort",
                avail1_2weeks: "1-2 Wochen",
                avail1_month: "Innerhalb von 1 Monat",
                availableLater: "Längere Vorlaufzeit erforderlich",
                submitBtn: "Bewerbung absenden",
                resultsTitle: "Bewerbungszusammenfassung",
                nextSteps: "Wir werden Ihre Bewerbung prüfen und uns innerhalb von 48 Stunden bei Ihnen melden, wenn eine potenzielle Übereinstimmung besteht."
            },
            ar: {
                companyIntroTitle: "أهلاً بك في ميوايز!",
                companyIntroText: "ميوايز هي منصة مبتكرة وشاملة مصممة لتمكين المبدعين العصريين ورواد الأعمال عبر الإنترنت. نحن نقدم مجموعة شاملة من الأدوات لبيع المنتجات الرقمية، وبناء المجتمعات، وإنشاء الدورات التدريبية، وإدارة الأعمال بكفاءة. مهمتنا هي تبسيط ريادة الأعمال عبر الإنترنت، مما يتيح للمبدعين التركيز على ما يجيدونه: خلق قيمة لجمهورهم.",
                infoBoxText: "لا تعرف ماذا نفعل؟ أو تريد معرفة المزيد؟ اذهب إلى <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "هام - اقرأ بعناية",
                warningList: [
                    "هذه فرصة شراكة قائمة على الأسهم (نسبة الملكية)",
                    "لدى ميوايز أكثر من 30,000 مستخدم، وكانت رقم 1 في Product Hunt، واستثمرت 30,000 دولار",
                    "لقد خصصنا 30,000 دولار للتسويق/النمو",
                    "تلقى الفريق السابق عروضًا من مطوري Meta/Apple السابقين (طُلب إجمالي 45٪ من الأسهم)",
                    "إذا كنت تبحث عن راتب/دفع فوري، فهذه الفرصة ليست لك",
                    "يجب على المرشحين الجادين الذين يؤمنون بالشراكات القائمة على الأسهم فقط المضي قدمًا"
                ],
                confirmationTitle: "تأكيد الفرصة",
                confirmLabel: "هل تؤكد أن هذه الفرصة تتعلق بشراكة ميوايز القائمة على الأسهم؟ *",
                confirmYes: "نعم، أنا أفهم أن هذه شراكة قائمة على الأسهم",
                confirmNo: "لا، كنت أتوقع شيئًا مختلفًا",
                personalTitle: "معلومات شخصية",
                nameLabel: "الاسم الكامل *",
                emailLabel: "عنوان البريد الإلكتروني *",
                locationLabel: "الموقع (البلد/المدينة) *",
                linkedinLabel: "ملف LinkedIn",
                experienceTitle: "الخبرة والمهارات",
                roleInterestLabel: "ما هو الدور الذي يثير اهتمامك أكثر؟ *",
                productRole: "قائد استراتيجية المنتج",
                partnershipsRole: "الشراكات الاستراتيجية",
                devRole: "تطوير المنتج / البرمجة",
                socialMediaRole: "إدارة وسائل التواصل الاجتماعي والمجتمع",
                contentCopyRole: "قائد المحتوى وكتابة الإعلانات",
                dataAnalyticsRole: "أخصائي البيانات والتحليلات",
                salesLeadRole: "قائد المبيعات",
                marketingLeadRole: "قائد التسويق",
                operationsLeadRole: "قائد العمليات",
                legalCounselRole: "مستشار قانوني",
                uiUxDesignerRole: "مصمم واجهة المستخدم / تجربة المستخدم",
                dataScientistRole: "عالم البيانات",
                customerSuccessRole: "قائد نجاح العملاء",
                financeControllerRole: "مراقب مالي",
                businessAnalystRole: "محلل أعمال",
                growthLeadRole: "قائد النمو",
                allRoles: "جميع الأدوار تثير اهتمامي",
                experienceYearsLabel: "سنوات الخبرة ذات الصلة؟ *",
                exp0_2: "0-2 سنة",
                exp3_5: "3-5 سنوات",
                exp6_10: "6-10 سنوات",
                exp10_plus: "10+ سنوات",
                skillsLabel: "اختر مهاراتك الرئيسية (اختر واحدة أو أكثر):", 
                skill1: "استراتيجية المنتج",
                skill2: "تطوير الأعمال",
                skill3: "الشراكات الاستراتيجية",
                skill4: "التسويق/النمو",
                skill5: "تقني/تطوير/برمجة",
                skill6: "جمع التبرعات",
                skill7: "الذكاء الاصطناعي/تعلم الآلة",
                skill8: "منصات التجارة الإلكترونية",
                skill9: "أنظمة إدارة علاقات العملاء (CRM)",
                skill10: "بناء المجتمع",
                skill11: "كتابة المحتوى (Copywriting)",
                skill12: "تحسين محركات البحث (SEO)",
                skill13: "تحرير الفيديو",
                skill14: "التصميم الجرافيكي",
                skill15: "تحليل البيانات",
                skill16: "إدارة المشاريع",
                skill17: "قانوني ومتوافق",
                skill18: "استراتيجية المبيعات",
                skill19: "المالية والمحاسبة",
                skill20: "الموارد البشرية",
                companiesLabel: "الشركات السابقة/الإنجازات البارزة *",
                previousCompaniesPlaceholder: "اذكر الشركات التي عملت بها، الشركات الناشئة التي أنشأتها، الإنجازات الرئيسية، إلخ.",
                compensationTitle: "التعويض والالتزام",
                partnershipTypeLabel: "ما نوع الشراكة التي تبحث عنها؟ *",
                partnershipType1: "شراكة قائمة على الأسهم",
                partnershipType2: "مختلط (راتب + أسهم)",
                partnershipType3: "تعويض مالي مقابل عمل/محفظة أعمال",
                upfrontFeeLabel: "الرسوم الأولية المطلوبة (بالدولار الأمريكي) *",
                upfrontFeePlaceholder: "على سبيل المثال، 5000",
                monthlyFeeLabel: "الرسوم الشهرية المطلوبة (بالدولار الأمريكي) *",
                monthlyFeePlaceholder: "على سبيل المثال، 1000",
                businessDescriptionLabel: "صِف العمل/محفظة الأعمال التي تقدمها *",
                businessDescriptionPlaceholder: "قدم تفاصيل حول عملك، وأصوله، وإيراداته، وقاعدة المستخدمين، أو محفظة الأعمال التي تقدمها مقابل تعويض مالي.",
                monthlyRevenueShareLabel: "حصة الإيرادات الشهرية المطلوبة (بالدولار الأمريكي) للنموذج المختلط *",
                monthlyRevenueSharePlaceholder: "على سبيل المثال، 500",
                equityAcceptanceLabel: "هل أنت مرتاح للتعويض القائم على الأسهم فقط في البداية؟ *",
                equityYesFull: "نعم، أنا مرتاح تمامًا للتعويض القائم على الأسهم فقط",
                equityYesConditions: "نعم، ولكن مع بعض الشروط",
                equityHybrid: "أفضل نموذجًا مختلطًا (راتب صغير + أسهم)",
                equityNo: "لا، أحتاج إلى دفع فوري",
                equityExpectationLabel: "ما هي النسبة المئوية للأسهم التي تبدو عادلة لمساهمتك؟ *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "قابل للتفاوض بناءً على القيمة",
                timeCommitmentLabel: "الالتزام بالوقت الذي يمكنك تقديمه؟ *",
                timePartTime: "دوام جزئي (10-20 ساعة/أسبوع)",
                timeSubstantial: "كبير (30-40 ساعة/أسبوع)",
                timeFullTime: "التزام بدوام كامل",
                flexible: "مرن بناءً على الاحتياجات",
                strategicTitle: "أسئلة استراتيجية",
                platformToolExpLabel: "الخبرة في ميزات المنصة الشبيهة بميوايز أو الأدوات ذات الصلة؟ *",
                platformToolExperiencePlaceholder: "اذكر تفاصيل خبرتك في منصات مثل Shopify، Teachable، ActiveCampaign، HubSpot، Hootsuite، إلخ، أو ميزات محددة مثل التجارة الإلكترونية، إدارة علاقات العملاء (CRM)، منصات الدورات التدريبية، إدارة وسائل التواصل الاجتماعي، أتمتة الذكاء الاصطناعي، أو أنظمة الضمان.",
                creatorEntrepreneurExpLabel: "الخبرة في العمل مع 'المبدعين العصريين' أو 'رواد الأعمال عبر الإنترنت'؟ *",
                creatorEntrepreneurExperiencePlaceholder: "صِف خبرتك في إنشاء المحتوى، أو التدريب عبر الإنترنت، أو التسويق الرقمي، أو إدارة عمل تجاري عبر الإنترنت.",
                mewayzVisionLabel: "رؤيتك الاستراتيجية لنمو ميوايز؟ *",
                mewayzGrowthVisionPlaceholder: "فكر في ميزات محددة مثل الأتمتة المدعومة بالذكاء الاصطناعي، أو التكاملات (Zapier، بوابات الدفع)، أو تعزيز بناء المجتمع. كن موجزًا.",
                networkLabel: "صِف شبكتك المهنية *",
                networkPlaceholder: "ما هي الاتصالات التي لديك والتي يمكن أن تفيد ميوايز؟ (المستثمرون، الشركاء، العملاء، إلخ.)",
                valuePropositionLabel: "ما القيمة الفريدة التي ستقدمها لميوايز؟ *",
                valuePropositionPlaceholder: "كن محددًا بشأن ما يجعلك الشريك المناسب",
                challengesLabel: "ما هي التحديات الكبرى التي تراها لمنصة أعمال شاملة مثل ميوايز؟",
                challengesPlaceholder: "شارك وجهة نظرك حول تحديات الصناعة وكيف يمكن لميوايز التغلب عليها.",
                finalTitle: "التقييم النهائي",
                whyNowLabel: "لماذا أنت مهتم بالانضمام إلى ميوايز الآن؟ *",
                whyNowPlaceholder: "ما الذي يجذبك إلى هذه الفرصة في هذه المرحلة؟",
                financialLabel: "الوضع المالي الحالي (تقييم صادق) *",
                financialStable: "مستقر ماليًا، يمكنني العمل مقابل الأسهم",
                financialRunway: "لدي بعض الاحتياطي، أفضّل النموذج المختلط",
                financialNeed: "أحتاج إلى دخل فوري",
                financialPrivate: "أفضّل عدم الذكر",
                availabilityLabel: "متى يمكنك البدء؟ *",
                availableNow: "فورًا",
                avail1_2weeks: "1-2 أسابيع",
                avail1_month: "خلال شهر واحد",
                availableLater: "مطلوب جدول زمني أطول",
                submitBtn: "إرسال الطلب",
                resultsTitle: "ملخص الطلب",
                nextSteps: "سنقوم بمراجعة طلبك والرد عليك في غضون 48 ساعة إذا كان هناك تطابق محتمل."
            },
            zh: {
                companyIntroTitle: "欢迎来到 Mewayz！",
                companyIntroText: "Mewayz 是一个创新的多合一平台，旨在赋能现代创作者和在线企业家。我们提供一整套全面的工具，用于销售数字产品、建立社区、创建课程和高效管理业务。我们的使命是简化在线创业，让创作者能够专注于他们最擅长的事情：为受众创造价值。",
                infoBoxText: "不知道我们做什么？或者想了解更多？请访问 <a href='https://mewayz.com'>mewayz.com</a>",
                importantNote: "重要提示 - 请仔细阅读",
                warningList: [
                    "这是一个基于股权的合作机会（百分比所有权）",
                    "Mewayz 拥有 30,000+ 用户，在 Product Hunt 上排名第一，并已投资 30,000 美元",
                    "我们已分配 30,000 美元用于市场推广/增长",
                    "前团队收到了来自前 Meta/Apple 开发者的报价（要求总股权的 45%）",
                    "如果您正在寻找即时薪水/报酬，此机会不适合您",
                    "只有相信股权合作的认真候选人才能继续进行"
                ],
                confirmationTitle: "机会确认",
                confirmLabel: "您确认这是关于 Mewayz 股权合作机会的吗？ *",
                confirmYes: "是的，我理解这是关于股权合作的",
                confirmNo: "不，我期待的是别的东西",
                personalTitle: "个人信息",
                nameLabel: "全名 *",
                emailLabel: "电子邮件地址 *",
                locationLabel: "地点（国家/城市） *",
                linkedinLabel: "领英个人资料",
                experienceTitle: "经验与技能",
                roleInterestLabel: "您最感兴趣的职位是？ *",
                productRole: "产品战略负责人",
                partnershipsRole: "战略合作",
                devRole: "产品开发 / 编程",
                socialMediaRole: "社交媒体和社区管理",
                contentCopyRole: "内容和文案负责人",
                dataAnalyticsRole: "数据和分析专家",
                salesLeadRole: "销售负责人",
                marketingLeadRole: "市场营销负责人",
                operationsLeadRole: "运营负责人",
                legalCounselRole: "法律顾问",
                uiUxDesignerRole: "UI/UX 设计师",
                dataScientistRole: "数据科学家",
                customerSuccessRole: "客户成功负责人",
                financeControllerRole: "财务总监",
                businessAnalystRole: "业务分析师",
                growthLeadRole: "增长负责人",
                allRoles: "所有职位都感兴趣",
                experienceYearsLabel: "相关经验年限？ *",
                exp0_2: "0-2 年",
                exp3_5: "3-5 年",
                exp6_10: "6-10 年",
                exp10_plus: "10+ 年",
                skillsLabel: "选择您的关键技能（选择一项或更多）：", 
                skill1: "产品战略",
                skill2: "业务发展",
                skill3: "战略合作",
                skill4: "市场推广/增长",
                skill5: "技术/开发/编程",
                skill6: "融资",
                skill7: "人工智能/机器学习",
                skill8: "电子商务平台",
                skill9: "CRM 系统",
                skill10: "社区建设",
                skill11: "文案",
                skill12: "SEO 优化",
                skill13: "视频编辑",
                skill14: "平面设计",
                skill15: "数据分析",
                skill16: "项目管理",
                skill17: "法律与合规",
                skill18: "销售策略",
                skill19: "财务与会计",
                skill20: "人力资源",
                companiesLabel: "曾任职公司/显著成就 *",
                previousCompaniesPlaceholder: "列出您曾工作过的公司、您创办的初创公司、主要成就等。",
                compensationTitle: "薪酬与承诺",
                partnershipTypeLabel: "您正在寻求哪种类型的合作？ *",
                partnershipType1: "基于股权的合作",
                partnershipType2: "混合（薪水 + 股权）",
                partnershipType3: "针对业务/作品集的货币补偿",
                upfrontFeeLabel: "期望预付费用 (美元) *",
                upfrontFeePlaceholder: "例如，5000",
                monthlyFeeLabel: "期望月度费用 (美元) *",
                monthlyFeePlaceholder: "例如，1000",
                businessDescriptionLabel: "描述您正在提供的业务/作品集 *",
                businessDescriptionPlaceholder: "提供有关您的业务、其资产、收入、用户群或您正在寻求货币补偿的作品集的详细信息。",
                monthlyRevenueShareLabel: "混合模式的期望月度收入分成 (美元) *",
                monthlyRevenueSharePlaceholder: "例如，500",
                equityAcceptanceLabel: "您是否愿意初期仅接受股权作为报酬？ *",
                equityYesFull: "是的，我完全愿意仅接受股权报酬",
                equityYesConditions: "是的，但有一些条件",
                equityHybrid: "我更喜欢混合模式（少量薪水 + 股权）",
                equityNo: "不，我需要立即获得报酬",
                equityExpectationLabel: "您认为您的贡献应获得多少股权百分比？ *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%+",
                negotiable: "根据价值开放谈判",
                timeCommitmentLabel: "您可以提供的时间承诺？ *",
                timePartTime: "兼职（10-20 小时/周）",
                timeSubstantial: "大量（30-40 小时/周）",
                timeFullTime: "全职承诺",
                flexible: "根据需要灵活",
                strategicTitle: "战略问题",
                platformToolExpLabel: "您是否有类似 Mewayz 平台功能或相关工具的经验？ *",
                platformToolExperiencePlaceholder: "详细说明您使用 Shopify、Teachable、ActiveCampaign、HubSpot、Hootsuite 等平台或电子商务、CRM、课程平台、社交媒体管理、AI 自动化或托管系统等特定功能的经验。",
                creatorEntrepreneurExpLabel: "您有与“现代创作者”或“在线企业家”合作的经验吗？ *",
                creatorEntrepreneurExperiencePlaceholder: "描述您在内容创作、在线辅导、数字营销或管理在线业务方面的经验。",
                mewayzVisionLabel: "您对 Mewayz 增长的战略愿景是什么？ *",
                mewayzGrowthVisionPlaceholder: "考虑特定功能，例如 AI 驱动的自动化、集成（Zapier、支付网关）或增强社区建设。请简洁。",
                networkLabel: "描述您的专业人脉 *",
                networkPlaceholder: "您有哪些可以使 Mewayz 受益的人脉？（投资者、合作伙伴、客户等）",
                valuePropositionLabel: "您将为 Mewayz 带来什么独特价值？ *",
                valuePropositionPlaceholder: "具体说明是什么让您成为合适的合作伙伴",
                challengesLabel: "您认为 Mewayz 这样的多合一业务平台面临的最大挑战是什么？",
                challengesPlaceholder: "分享您对行业挑战以及 Mewayz 如何克服这些挑战的看法。",
                finalTitle: "最终评估",
                whyNowLabel: "您现在为什么对加入 Mewayz 感兴趣？ *",
                whyNowPlaceholder: "在此阶段，此机会的哪些方面吸引您？",
                financialLabel: "当前财务状况（诚实评估） *",
                financialStable: "财务稳定，可以为股权工作",
                financialRunway: "有一些周转资金，更喜欢混合模式",
                financialNeed: "需要立即收入",
                financialPrivate: "不愿透露",
                availabilityLabel: "您何时可以开始工作？ *",
                availableNow: "立即",
                avail1_2weeks: "1-2 周",
                avail1_month: "1 个月内",
                availableLater: "需要更长时间",
                submitBtn: "提交申请",
                resultsTitle: "申请摘要",
                nextSteps: "我们将审查您的申请，如果匹配，将在 48 小时内与您联系。"
            },
            ja: {
                companyIntroTitle: "Mewayzへようこそ！",
                companyIntroText: "Mewayzは、現代のクリエイターやオンライン起業家を支援するために設計された、革新的なオールインワンプラットフォームです。デジタル製品の販売、コミュニティの構築、コースの作成、ビジネスの効率的な管理のための包括的なツールスイートを提供しています。私たちの使命は、オンライン起業を簡素化し、クリエイターが最も得意なこと、つまり視聴者に価値を創造することに集中できるようにすることです。",
                infoBoxText: "私たちのサービスについてご存知ないですか？ 詳細を知りたいですか？ <a href='https://mewayz.com'>mewayz.com</a>をご覧ください",
                importantNote: "重要 - 注意深くお読みください",
                warningList: [
                    "これは株式ベースのパートナーシップ機会です（所有権の割合）",
                    "Mewayzは30,000人以上のユーザーを持ち、Product Huntで1位を獲得し、30,000ドルを投資しました",
                    "マーケティング/成長のために30,000ドルを割り当てています",
                    "以前のチームは元Meta/Apple開発者からオファーを受けました（総株式の45%を要求）",
                    "もしあなたが即座の給与/支払いを探しているなら、この機会はあなたには向いていません",
                    "株式パートナーシップを信じる真剣な候補者のみが進んでください"
                ],
                confirmationTitle: "機会の確認",
                confirmLabel: "Mewayzの株式パートナーシップ機会についてであることを確認しますか？ *",
                confirmYes: "はい、株式パートナーシップについて理解しています",
                confirmNo: "いいえ、何か違うものを期待していました",
                personalTitle: "個人情報",
                nameLabel: "氏名 *",
                emailLabel: "Eメールアドレス *",
                locationLabel: "所在地（国/都市） *",
                linkedinLabel: "LinkedInプロフィール",
                experienceTitle: "経験とスキル",
                roleInterestLabel: "最も興味のある役割は何ですか？ *",
                productRole: "プロダクト戦略リード",
                partnershipsRole: "戦略的パートナーシップ",
                devRole: "プロダクト開発 / プログラミング",
                socialMediaRole: "ソーシャルメディア＆コミュニティマネジメント",
                contentCopyRole: "コンテンツ＆コピーライティングリード",
                dataAnalyticsRole: "データ＆アナリティクススペシャリスト",
                salesLeadRole: "セールスリード",
                marketingLeadRole: "マーケティングリード",
                operationsLeadRole: "オペレーションズリード",
                legalCounselRole: "法務顧問",
                uiUxDesignerRole: "UI/UXデザイナー",
                dataScientistRole: "データサイエンティスト",
                customerSuccessRole: "カスタマーサクセスリード",
                financeControllerRole: "財務コントローラー",
                businessAnalystRole: "ビジネスアナリスト",
                growthLeadRole: "グロースリード",
                allRoles: "すべての役割に興味があります",
                experienceYearsLabel: "関連する経験年数？ *",
                exp0_2: "0-2年",
                exp3_5: "3-5年",
                exp6_10: "6-10年",
                exp10_plus: "10+年",
                skillsLabel: "主要スキルを選択してください（1つ以上選択）：", 
                skill1: "プロダクト戦略",
                skill2: "事業開発",
                skill3: "戦略的パートナーシップ",
                skill4: "マーケティング/成長",
                skill5: "技術/開発/プログラミング",
                skill6: "資金調達",
                skill7: "AI/機械学習",
                skill8: "Eコマースプラットフォーム",
                skill9: "CRMシステム",
                skill10: "コミュニティ構築",
                skill11: "コピーライティング",
                skill12: "SEO最適化",
                skill13: "動画編集",
                skill14: "グラフィックデザイン",
                skill15: "データ分析",
                skill16: "プロジェクト管理",
                skill17: "法務・コンプライアンス",
                skill18: "営業戦略",
                skill19: "財務・会計",
                skill20: "人事",
                companiesLabel: "以前の会社/注目すべき実績 *",
                previousCompaniesPlaceholder: "これまでに勤務した会社、立ち上げたスタートアップ、主要な実績などを記載してください。",
                compensationTitle: "報酬とコミットメント",
                partnershipTypeLabel: "どのようなパートナーシップを求めていますか？ *",
                partnershipType1: "株式ベースのパートナーシップ",
                partnershipType2: "ハイブリッド（給与＋株式）",
                partnershipType3: "ビジネス/ポートフォリオに対する金銭的報酬",
                upfrontFeeLabel: "希望する前払い金 (USD) *",
                upfrontFeePlaceholder: "例: 5000",
                monthlyFeeLabel: "希望する月額料金 (USD) *",
                monthlyFeePlaceholder: "例: 1000",
                businessDescriptionLabel: "提供するビジネス/ポートフォリオを説明してください *",
                businessDescriptionPlaceholder: "あなたのビジネス、その資産、収益、ユーザーベース、または金銭的報酬と引き換えに提供する仕事のポートフォリオに関する詳細を提供してください。",
                monthlyRevenueShareLabel: "ハイブリッドモデルにおける希望月間レベニューシェア (USD) *",
                monthlyRevenueSharePlaceholder: "例: 500",
                equityAcceptanceLabel: "初期の報酬が株式のみであることに抵抗はありませんか？ *",
                equityYesFull: "はい、株式のみの報酬で全く問題ありません",
                equityYesConditions: "はい、ただし条件付きで",
                equityHybrid: "ハイブリッドモデル（少額の給与＋株式）を希望します",
                equityNo: "いいえ、即座の支払いが必要です",
                equityExpectationLabel: "あなたの貢献に見合う公正な株式の割合はどのくらいだと思いますか？ *",
                equityLess1: "<1%",
                equity1_2: "1-2%",
                equity2_5: "2-5%",
                equity5_10: "5-10%",
                equity10_15: "10-15%",
                equity15_20: "15-20%",
                equity20_25: "20-25%",
                equity25_plus: "25%以上",
                negotiable: "価値に基づいて交渉可能",
                timeCommitmentLabel: "提供できる時間のコミットメントは？ *",
                timePartTime: "パートタイム（週10-20時間）",
                timeSubstantial: "相当な時間（週30-40時間）",
                timeFullTime: "フルタイムコミットメント",
                flexible: "必要に応じて柔軟に対応",
                strategicTitle: "戦略的な質問",
                platformToolExpLabel: "Mewayzのようなプラットフォーム機能または関連ツールに関する経験はありますか？ *",
                platformToolExperiencePlaceholder: "Shopify、Teachable、ActiveCampaign、HubSpot、Hootsuiteなどのプラットフォーム、またはEコマース、CRM、コースプラットフォーム、ソーシャルメディア管理、AI自動化、エスクローシステムなどの特定の機能に関する経験を詳細に説明してください。",
                creatorEntrepreneurExpLabel: "「モダンクリエイター」または「オンライン起業家」との連携経験はありますか？ *",
                creatorEntrepreneurExperiencePlaceholder: "コンテンツ作成、オンラインコーチング、デジタルマーケティング、またはオンラインビジネスの管理におけるあなたの経験を説明してください。",
                mewayzVisionLabel: "Mewayzの成長に対するあなたの戦略的ビジョンは？ *",
                mewayzGrowthVisionPlaceholder: "AIを活用した自動化、統合（Zapier、決済ゲートウェイ）、コミュニティ構築の強化などの特定の機能を検討してください。簡潔に。",
                networkLabel: "あなたのプロフェッショナルネットワークを説明してください *",
                networkPlaceholder: "Mewayzに利益をもたらす可能性のあるコネクションは何ですか？（投資家、パートナー、クライアントなど）",
                valuePropositionLabel: "Mewayzにどのような独自の価値をもたらしますか？ *",
                valuePropositionPlaceholder: "あなたが最適なパートナーである理由を具体的に説明してください",
                challengesLabel: "Mewayzのようなオールインワンビジネスプラットフォームにとって最大の課題は何だと思いますか？",
                challengesPlaceholder: "業界の課題とMewayzがそれらをどのように克服できるかについてのあなたの見解を共有してください。",
                finalTitle: "最終評価",
                whyNowLabel: "なぜ今Mewayzへの参加に興味がありますか？ *",
                whyNowPlaceholder: "この段階で、この機会の何があなたを惹きつけていますか？",
                financialLabel: "現在の財政状況（正直な評価） *",
                financialStable: "経済的に安定しており、株式のために働くことができます",
                financialRunway: "ある程度の資金があり、ハイブリッドを希望します",
                financialNeed: "即座の収入が必要です",
                financialPrivate: "言いたくない",
                availabilityLabel: "いつから開始できますか？ *",
                availableNow: "即座に",
                avail1_2weeks: "1-2週間以内",
                avail1_month: "1ヶ月以内",
                availableLater: "より長い期間が必要",
                submitBtn: "申請を送信",
                resultsTitle: "申請概要",
                nextSteps: "あなたの申請を審査し、適合する可能性がある場合は48時間以内にご連絡いたします。"
            }
        };

        // Function to update the UI based on selected language
        function changeLanguage() {
            const lang = document.getElementById('languageSelect').value;
            const t = translations[lang] || translations.en;
            console.log("Applying translations for language:", lang); // Debugging

            // Directly update text content for elements by ID
            const textElements = [
                'companyIntroTitle', 'companyIntroText', 'infoBoxText', 'importantNote', 'confirmationTitle', 'confirmLabel', 'confirmYes', 'confirmNo',
                'personalTitle', 'nameLabel', 'emailLabel', 'locationLabel', 'linkedinLabel',
                'experienceTitle', 'roleInterestLabel', 'productRole', 'partnershipsRole', 'devRole', 
                'socialMediaRole', 'contentCopyRole', 'dataAnalyticsRole',
                'salesLeadRole', 'marketingLeadRole', 'operationsLeadRole', 'legalCounselRole', 'uiUxDesignerRole', // New roles
                'dataScientistRole', 'customerSuccessRole', 'financeControllerRole', 'businessAnalystRole', 'growthLeadRole', // More new roles
                'allRoles', 'experienceYearsLabel', 'exp0_2', 'exp3_5', 'exp6_10', 'exp10_plus',
                'skillsLabel', 'skill1', 'skill2', 'skill3', 'skill4', 'skill5', 'skill6', 'skill7', 'skill8', 'skill9', 'skill10',
                'skill11', 'skill12', 'skill13', 'skill14', 'skill15', 'skill16', 'skill17', 'skill18', 'skill19', 'skill20', // New skills
                'companiesLabel', 'compensationTitle', 
                'partnershipTypeLabel', 'partnershipType1', 'partnershipType2', 'partnershipType3', // Partnership type fields
                'upfrontFeeLabel', 'monthlyFeeLabel', 'businessDescriptionLabel', // Monetary compensation fields
                'monthlyRevenueShareLabel', // New revenue share label
                'equityAcceptanceLabel', 'equityYesFull', 'equityYesConditions',
                'equityHybrid', 'equityNo', 'equityExpectationLabel', 'equityLess1', 'equity1_2', 'equity2_5', 'equity5_10', 'equity10_15',
                'equity15_20', 'equity20_25', 'equity25_plus', 'negotiable', // Expanded equity options
                'timeCommitmentLabel', 'timePartTime', 'timeSubstantial', 'timeFullTime',
                'flexible', 'strategicTitle', 'platformToolExpLabel', 'creatorEntrepreneurExpLabel', 'mewayzVisionLabel',
                'networkLabel', 'valuePropositionLabel', 'challengesLabel', 'finalTitle', 'whyNowLabel', 'financialLabel',
                'financialStable', 'financialRunway', 'financialNeed', 'financialPrivate', 'availabilityLabel',
                'availableNow', 'avail1_2weeks', 'avail1_month', 'availableLater', 'submitBtn', 'resultsTitle', 'nextSteps'
            ];

            textElements.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    // Special handling for elements whose text might contain HTML (like infoBoxText)
                    if (id === 'infoBoxText' || id === 'companyIntroText') {
                        element.innerHTML = t[id];
                    } else if (element.tagName === 'OPTION' || (element.tagName === 'SPAN' && element.closest('.checkbox-item'))) {
                        // For option tags and spans within checkbox-item, update textContent directly
                        element.textContent = t[id];
                    } else if (id === 'submitBtn') { // Special handling for submit button text
                        document.getElementById('submitBtnText').textContent = t[id];
                    } 
                    else {
                        // For all other text elements, safely update textContent
                        element.textContent = t[id];
                    }
                } else {
                    console.warn(`Element with ID '${id}' not found for translation.`);
                }
            });

            // Update warning list separately
            const warningListElement = document.getElementById('warningList');
            if (warningListElement && t.warningList) {
                warningListElement.innerHTML = t.warningList.map(item => `<li>${item}</li>`).join('');
            }

            // Update placeholders
            const placeholders = {
                'previousCompanies': 'previousCompaniesPlaceholder',
                'platformToolExperience': 'platformToolExperiencePlaceholder',
                'creatorEntrepreneurExperience': 'creatorEntrepreneurExperiencePlaceholder',
                'mewayzGrowthVision': 'mewayzGrowthVisionPlaceholder',
                'network': 'networkPlaceholder',
                'valueProposition': 'valuePropositionPlaceholder',
                'challenges': 'challengesPlaceholder',
                'whyNow': 'whyNowPlaceholder',
                'upfrontFee': 'upfrontFeePlaceholder', // Monetary placeholders
                'monthlyFee': 'monthlyFeePlaceholder',
                'businessDescription': 'businessDescriptionPlaceholder',
                'monthlyRevenueShare': 'monthlyRevenueSharePlaceholder' // New revenue share placeholder
            };

            for (const inputName in placeholders) {
                const element = document.querySelector(`[name="${inputName}"]`); // Use general selector for input/textarea
                if (element && t[placeholders[inputName]]) {
                    element.placeholder = t[placeholders[inputName]];
                } else if (!element) {
                     console.warn(`Element with name "${inputName}" not found for placeholder translation.`);
                }
            }
        }

        // --- JavaScript for Conditional Compensation Fields ---
        document.addEventListener('DOMContentLoaded', () => {
            const partnershipTypeRadios = document.querySelectorAll('input[name="partnershipType"]');
            const equityCompensationGroup = document.getElementById('equityCompensationGroup');
            const monetaryCompensationGroup = document.getElementById('monetaryCompensationGroup');
            const hybridRevenueShareGroup = document.getElementById('hybridRevenueShareGroup'); // New group for hybrid revenue share

            const equityAcceptanceSelect = document.getElementById('equityAcceptance');
            const equityExpectationSelect = document.getElementById('equityExpectation');
            const monthlyRevenueShareInput = document.getElementById('monthlyRevenueShare');
            const upfrontFeeInput = document.getElementById('upfrontFee');
            const monthlyFeeInput = document.getElementById('monthlyFee');
            const businessDescriptionTextarea = document.getElementById('businessDescription');


            function toggleCompensationFields() {
                const selectedRadio = document.querySelector('input[name="partnershipType"]:checked');
                const selectedType = selectedRadio ? selectedRadio.value : ''; // Get the actual value attribute

                // Reset all required and disabled attributes first for all relevant inputs/selects
                if (equityAcceptanceSelect) {
                    equityAcceptanceSelect.removeAttribute('required');
                    equityAcceptanceSelect.disabled = true; // Disable by default
                }
                if (equityExpectationSelect) {
                    equityExpectationSelect.removeAttribute('required');
                    equityExpectationSelect.disabled = true; // Disable by default
                }
                if (monthlyRevenueShareInput) {
                    monthlyRevenueShareInput.removeAttribute('required');
                    monthlyRevenueShareInput.disabled = true; // Disable by default
                }
                if (upfrontFeeInput) {
                    upfrontFeeInput.removeAttribute('required');
                    upfrontFeeInput.disabled = true; // Disable by default
                }
                if (monthlyFeeInput) {
                    monthlyFeeInput.removeAttribute('required');
                    monthlyFeeInput.disabled = true; // Disable by default
                }
                if (businessDescriptionTextarea) {
                    businessDescriptionTextarea.removeAttribute('required');
                    businessDescriptionTextarea.disabled = true; // Disable by default
                }

                // Hide all groups initially
                if (equityCompensationGroup) equityCompensationGroup.style.display = 'none';
                if (hybridRevenueShareGroup) hybridRevenueShareGroup.style.display = 'none';
                if (monetaryCompensationGroup) monetaryCompensationGroup.style.display = 'none';


                if (selectedType === 'equity') {
                    if (equityCompensationGroup) equityCompensationGroup.style.display = 'block';
                    
                    if (equityAcceptanceSelect) {
                        equityAcceptanceSelect.setAttribute('required', 'required');
                        equityAcceptanceSelect.disabled = false; // Enable for equity
                        equityAcceptanceSelect.value = ''; // Ensure equityAcceptance is reset for pure equity
                    }
                    if (equityExpectationSelect) {
                        equityExpectationSelect.setAttribute('required', 'required');
                        equityExpectationSelect.disabled = false; // Enable for equity
                      // Set 'equityAcceptance' to 'I prefer a hybrid model (small salary + equity)'
                     //   const hybridOption = equityAcceptanceSelect.querySelector('option[value="hybrid"]');
                       // if (hybridOption) {
                         //   equityAcceptanceSelect.value = 'yes-full';
                       // } else {
                         //   console.warn("Option with value 'hybrid' not found in equityAcceptance select.");
                       // }
                    }

                } else if (selectedType === 'hybrid') {
                    if (equityCompensationGroup) equityCompensationGroup.style.display = 'block';
                    if (hybridRevenueShareGroup) hybridRevenueShareGroup.style.display = 'block';

                    if (equityAcceptanceSelect) {
                        equityAcceptanceSelect.setAttribute('required', 'required');
                        equityAcceptanceSelect.disabled = false; // Enable for hybrid
                        // Set 'equityAcceptance' to 'I prefer a hybrid model (small salary + equity)'
                       // const hybridOption = equityAcceptanceSelect.querySelector('option[value="hybrid"]');
                       // if (hybridOption) {
                       //     equityAcceptanceSelect.value = 'hybrid';
                       // } else {
                       //     console.warn("Option with value 'hybrid' not found in equityAcceptance select.");
                       // }
                    }
                    if (equityExpectationSelect) {
                        equityExpectationSelect.setAttribute('required', 'required');
                        equityExpectationSelect.disabled = false; // Enable for hybrid
                    }
                    if (monthlyRevenueShareInput) {
                        monthlyRevenueShareInput.setAttribute('required', 'required');
                        monthlyRevenueShareInput.disabled = false; // Enable for hybrid
                    }

                } else if (selectedType === 'monetary_business') {
                    if (monetaryCompensationGroup) monetaryCompensationGroup.style.display = 'block';

                    if (upfrontFeeInput) {
                        upfrontFeeInput.setAttribute('required', 'required');
                        upfrontFeeInput.disabled = false; // Enable for monetary
                    }
                    if (monthlyFeeInput) {
                        monthlyFeeInput.setAttribute('required', 'required');
                        monthlyFeeInput.disabled = false; // Enable for monetary
                    }
                    if (businessDescriptionTextarea) {
                        businessDescriptionTextarea.setAttribute('required', 'required');
                        businessDescriptionTextarea.disabled = false; // Enable for monetary
                    }
                }
            }

            partnershipTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleCompensationFields);
            });

            // Initial call to set correct visibility, requirements, and disabled states on page load
            toggleCompensationFields(); 
        });


        // JavaScript to handle form submission and display server-generated report
        document.getElementById('vettingForm').addEventListener('submit', async function(e) {
            e.preventDefault(); 

            const formData = new FormData(this);
            const statusMessageDiv = document.getElementById('statusMessage');
            const resultsSection = document.getElementById('results');
            const resultsContentPre = document.getElementById('resultsContent');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitSpinner = document.getElementById('submitSpinner');

            // Show loading state
            submitBtn.disabled = true;
            submitBtnText.style.display = 'none';
            submitSpinner.style.display = 'block';
            statusMessageDiv.style.display = 'block';
            statusMessageDiv.className = 'loading'; 
            statusMessageDiv.textContent = 'Submitting application... Please wait.';

            try {
                // Explicitly set the API endpoint here, as action is removed from form
                const response = await fetch('/api/send-mewayz-email', { 
                    method: 'POST', // Use 'POST' directly
                    body: formData,
                    headers: {
                        'Accept': 'application/json' // Indicate that we prefer JSON response
                    }
                });

                if (response.ok) {
                    const result = await response.json(); // Expect JSON response
                    
                    // Assuming the backend returns { report: "..." }
                    if (result && result.report) {
                        statusMessageDiv.className = 'success';
                        statusMessageDiv.textContent = 'Application submitted successfully!';
                        
                        document.getElementById('vettingForm').style.display = 'none'; 
                        resultsSection.style.display = 'block';
                        resultsContentPre.textContent = result.report; // Display server-generated report
                        
                        resultsSection.scrollIntoView({ behavior: 'smooth' });
                    } else {
                        // Fallback if backend doesn't return expected JSON format
                        statusMessageDiv.className = 'error';
                        statusMessageDiv.textContent = 'Submission successful, but failed to retrieve report from server. Please check console for details.';
                        console.error('Server response missing "report" field:', result);
                        // Optionally, you could still try to generate client-side report here
                        // const data = {}; for (let [key, value] of formData.entries()) { /* ... */ }
                        // resultsContentPre.textContent = generateResults(data);
                        // resultsSection.style.display = 'block';
                    }

                } else {
                    const errorJson = await response.json(); // Try to parse error JSON
                    let errorMessage = `Submission failed: ${response.status} ${response.statusText}.`;
                    if (errorJson.message) {
                        errorMessage += ` ${errorJson.message}`;
                    }
                    if (errorJson.errors) {
                        // Display specific validation errors
                        for (const field in errorJson.errors) {
                            errorMessage += `\n- ${field}: ${errorJson.errors[field].join(', ')}`;
                        }
                    }
                    statusMessageDiv.className = 'error';
                    statusMessageDiv.textContent = errorMessage;
                    console.error('Form submission error:', errorJson);
                }
            } catch (error) {
                statusMessageDiv.className = 'error';
                statusMessageDiv.textContent = 'An error occurred during submission. Please check your connection and try again.';
                console.error('Network or unexpected error:', error);
            } finally {
                // Reset loading state
                submitBtn.disabled = false;
                submitBtnText.style.display = 'block';
                submitSpinner.style.display = 'none';
            }
        });

        // Add event listener for the Copy Report button
        document.getElementById('copyReportBtn').addEventListener('click', function() {
            const resultsContent = document.getElementById('resultsContent').textContent;
            const textarea = document.createElement('textarea');
            textarea.value = resultsContent;
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                const statusMessageDiv = document.getElementById('statusMessage');
                statusMessageDiv.style.display = 'block';
                statusMessageDiv.className = 'success';
                statusMessageDiv.textContent = 'Report copied to clipboard!';
                setTimeout(() => { statusMessageDiv.style.display = 'none'; }, 3000); 
            } catch (err) {
                console.error('Failed to copy text: ', err);
                const statusMessageDiv = document.getElementById('statusMessage');
                statusMessageDiv.style.display = 'block';
                statusMessageDiv.className = 'error';
                statusMessageDiv.textContent = 'Failed to copy report. Please copy manually.';
            } finally {
                document.body.removeChild(textarea);
            }
        });

        // IMPORTANT: This client-side `generateResults` function is now primarily for reference.
        // It's expected that your Laravel backend will generate and return the report string.
        // You would port this logic (or a similar version) to your Laravel controller/API handler.
        function generateResults(data) {
            let score = 0;
            let flags = [];
            let strengths = [];

            // Ensure skills is an array, as it comes from skill[] in HTML
            const selectedSkills = Array.isArray(data.skill) ? data.skill : [];

            // --- Critical Filter: Confirmation of Understanding ---
            if (data.confirmation !== 'yes') {
                flags.push("❌ DEAL BREAKER: Applicant does NOT understand this is an equity partnership opportunity.");
                score = 0; // Immediately set score to 0
                return formatReport(data, score, flags, strengths); // Return early with "SKIP"
            } else {
                strengths.push("✅ Confirmed understanding of equity partnership opportunity.");
                score += 10; // Positive initial score for understanding
            }

            // --- Scoring based on Partnership Type and Equity Expectations ---
            // Note: The partnershipType values in `data` will be 'equity', 'hybrid', 'monetary_business'
            // because that's what's sent from the form based on the radio button `value` attributes.
            if (data.partnershipType === 'equity' || data.partnershipType === 'hybrid') {
                if (data.equityAcceptance === 'no') {
                    flags.push("❌ DEAL BREAKER: Needs immediate payment (for equity/hybrid model).");
                    score = Math.max(0, score - 50); // Significant penalty
                } else if (data.equityAcceptance === 'yes-full') {
                    score += 25; // High value for full comfort
                    strengths.push("✅ Fully comfortable with equity-only compensation.");
                } else if (data.equityAcceptance === 'yes-conditions') {
                    score += 10;
                    flags.push("⚠️ Has conditions on equity acceptance.");
                } else if (data.equityAcceptance === 'hybrid') { // This `hybrid` value comes from the `equityAcceptance` select
                    score += 5;
                    strengths.push("✅ Prefers hybrid compensation model.");
                    if (!data.monthlyRevenueShare || parseFloat(data.monthlyRevenueShare) <= 0) {
                        flags.push("⚠️ Hybrid: Monthly Revenue Share not specified or invalid (required for hybrid).");
                        score = Math.max(0, score - 15);
                    } else {
                        strengths.push(`✅ Hybrid: Desired Monthly Revenue Share: $${data.monthlyRevenueShare}.`);
                        // Consider adding score based on realistic revenue share (e.g., lower is better)
                    }
                }

                // Equity Expectation for high-MRR startup
                if (data.equityExpectation === '25%+') {
                    flags.push("❌ High equity expectations (25%+ requested) for a high-MRR company.");
                    score = Math.max(0, score - 40); // Significant negative impact
                } else if (data.equityExpectation === '20-25%') {
                    flags.push("⚠️ Relatively high equity expectations (20-25% requested).");
                    score = Math.max(0, score - 20);
                } else if (data.equityExpectation === '15-20%') {
                    flags.push("⚠️ Moderate-high equity expectations (15-20% requested).");
                    score = Math.max(0, score - 10);
                } else if (data.equityExpectation === '10-15%') {
                    score += 5; // Reasonable for significant roles
                } else if (['<1%', '1-2%', '2-5%', '5-10%'].includes(data.equityExpectation)) {
                    score += 15; // More realistic and positive for high-MRR
                } else if (data.equityExpectation === 'negotiable') {
                    score += 10;
                    strengths.push("✅ Flexible on equity expectations.");
                }

            } else if (data.partnershipType === 'monetary_business') {
                // Scoring for monetary compensation for business/portfolio
                if (!data.upfrontFee && !data.monthlyFee) {
                    flags.push("❌ No monetary compensation details provided for business offer (required for this type).");
                    score = Math.max(0, score - 30);
                } else {
                    strengths.push("✅ Proposed monetary compensation for business/portfolio.");
                    if (data.upfrontFee && parseFloat(data.upfrontFee) > 0) score += 5; // Small positive for upfront
                    if (data.monthlyFee && parseFloat(data.monthlyFee) > 0) score += 5; // Small positive for monthly
                }

                if (!data.businessDescription || data.businessDescription.trim() === '') {
                    flags.push("⚠️ Business/Portfolio description not provided or too vague (required for this type).");
                    score = Math.max(0, score - 25);
                } else {
                    strengths.push("✅ Provided detailed business/portfolio description.");
                    score += 15; // Strong positive for a clear valuable offering
                }
            }

            // --- Financial Situation ---
            if (data.financialSituation === 'stable') {
                score += 20;
                strengths.push("✅ Financially stable, can work for equity.");
            } else if (data.financialSituation === 'some-runway') {
                score += 8;
                flags.push("⚠️ Has some financial runway, prefers hybrid.");
            } else if (data.financialSituation === 'need-income') {
                flags.push("⚠️ Warning: Needs immediate income – potentially a poor fit for equity-heavy role.");
                score = Math.max(0, score - 20); // Still a significant flag
            } else if (data.financialSituation === 'prefer-not-say') {
                flags.push("⚠️ Financial situation not disclosed.");
            }

            // --- Experience Years ---
            if (data.experienceYears === '10+') {
                score += 30; // Highly valued for high-MRR startup
                strengths.push("✅ 10+ years of highly relevant experience.");
            } else if (data.experienceYears === '6-10') {
                score += 20;
                strengths.push("✅ 6-10 years of solid relevant experience.");
            } else if (data.experienceYears === '3-5') {
                score += 10;
                strengths.push("✅ 3-5 years of relevant experience.");
            } else if (data.experienceYears === '0-2') {
                flags.push("⚠️ Limited relevant experience (0-2 years).");
                score = Math.max(0, score - 10);
            }

            // --- Time Commitment ---
            if (data.timeCommitment === 'full-time') {
                score += 20;
                strengths.push("✅ Committed to full-time engagement.");
            } else if (data.timeCommitment === 'substantial') {
                score += 10;
                strengths.push("✅ Offers substantial time commitment (30-40 hours/week).");
            } else if (data.timeCommitment === 'part-time') {
                flags.push("⚠️ Offers part-time commitment only (10-20 hours/week).");
                score = Math.max(0, score - 10);
            } else if (data.timeCommitment === 'flexible') {
                flags.push("⚠️ Flexible time commitment - requires clarification.");
            }

            // --- Skill Scoring (prioritize strategic, tech, growth for high MRR) ---
            const skillScoreMap = {
                'product-strategy': 8, 'partnerships': 7, 'development': 9, 'marketing': 6,
                'fundraising': 7, 'tech': 9, 'ai': 8, 'ecommerce': 6, 'crm': 5, 'community': 4,
                'copywriting': 4, 'seo': 5, 'video-editing': 3, 'graphic-design': 3,
                'data-analysis': 7, 'project-management': 5, 'legal': 6, 'sales': 7, 'finance': 7, 'hr': 3,
                'business-development': 7 // Added specific for BD as it's crucial for high MRR growth
            };
            selectedSkills.forEach(skill => {
                score += skillScoreMap[skill] || 0;
            });
            if (selectedSkills.length > 0) {
                strengths.push(`✅ Key skills identified: ${selectedSkills.join(', ')}.`);
            } else {
                flags.push("⚠️ No key skills selected.");
                score = Math.max(0, score - 5);
            }

            // --- Strategic Questions & General Fit ---
            if (!data.previousCompanies || data.previousCompanies.trim() === '') {
                flags.push("⚠️ Previous companies/achievements not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Provided previous companies/achievements.");
                score += 5;
            }
            if (!data.valueProposition || data.valueProposition.trim() === '') {
                flags.push("⚠️ Value proposition not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Provided clear value proposition.");
                score += 5;
            }
            if (!data.network || data.network.trim() === '') {
                flags.push("⚠️ Professional network description not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Described professional network.");
                score += 5;
            }
            if (!data.whyNow || data.whyNow.trim() === '') {
                flags.push("⚠️ Reason for joining now not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Provided motivation for joining now.");
                score += 5;
            }

            // New fields checks
            if (!data.platformToolExperience || data.platformToolExperience.trim() === '') {
                flags.push("⚠️ Experience with Mewayz-like platform features not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Provided platform/tool experience.");
                score += 5;
            }
            if (!data.creatorEntrepreneurExperience || data.creatorEntrepreneurExperience.trim() === '') {
                flags.push("⚠️ Experience with Modern Creators/Online Entrepreneurs not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Provided creator/entrepreneur experience.");
                score += 5;
            }
            if (!data.mewayzGrowthVision || data.mewayzGrowthVision.trim() === '') {
                flags.push("⚠️ Strategic vision for Mewayz's growth not provided.");
                score = Math.max(0, score - 5);
            } else {
                strengths.push("✅ Provided strategic vision for Mewayz.");
                score += 5;
            }

            score = Math.max(0, score); // Ensure score doesn't go below 0

            return formatReport(data, score, flags, strengths);
        }

        // Function to format the report
        function formatReport(data, score, flags, strengths) {
            let priority = 'LOW';
            if (score >= 70) priority = 'HIGH';
            else if (score >= 40) priority = 'MEDIUM';

            const now = new Date();
            const thaiTime = now.toLocaleString('en-US', {
                timeZone: 'Asia/Bangkok',
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });

            // Handle skills as an array
            const skillsText = Array.isArray(data.skill) && data.skill.length > 0 ? data.skill.join(', ') : 'None selected';
            
            // Collect compensation details based on type
            let compensationDetails = '';
            // Use the actual values from the radio buttons ('equity', 'hybrid', 'monetary_business')
            if (data.partnershipType === 'equity' || data.partnershipType === 'hybrid') {
                compensationDetails += `Equity Acceptance: ${data.equityAcceptance || 'Not specified'}\n`;
                compensationDetails += `Equity Expectation: ${data.equityExpectation || 'Not specified'}\n`;
                if (data.partnershipType === 'hybrid' && data.monthlyRevenueShare && parseFloat(data.monthlyRevenueShare) > 0) {
                    compensationDetails += `Desired Monthly Revenue Share: $${data.monthlyRevenueShare}\n`;
                }
            } else if (data.partnershipType === 'monetary_business') {
                compensationDetails += `Upfront Fee: ${data.upfrontFee ? `$${data.upfrontFee}` : 'Not specified'}\n`;
                compensationDetails += `Monthly Fee: ${data.monthlyFee ? `$${data.monthlyFee}` : 'Not specified'}\n`;
                compensationDetails += `Business/Portfolio Description: ${data.businessDescription || 'Not provided'}\n`;
            }


            return `
CANDIDATE ASSESSMENT REPORT 
============================

PRIORITY LEVEL: ${priority} (Score: ${score}/100)

BASIC INFO:
- Name: ${data.fullName || 'Not provided'}
- Email: ${data.email || 'Not provided'}
- Location: ${data.location || 'Not provided'}
- LinkedIn: ${data.linkedin || 'Not provided'}

ROLE & EXPERIENCE:
- Role Interest: ${data.roleInterest || 'Not specified'}
- Experience: ${data.experienceYears || 'Not specified'} years
- Skills: ${skillsText}

COMPENSATION EXPECTATIONS:
- Partnership Type: ${data.partnershipType || 'Not specified'}
${compensationDetails.trim()}
- Financial Situation: ${data.financialSituation || 'Not specified'}
- Time Commitment: ${data.timeCommitment || 'Not specified'}
- Availability: ${data.availability || 'Not specified'}

STRATEGIC INSIGHTS:
- Platform/Tool Experience: ${data.platformToolExperience || 'Not provided'}
- Creator/Entrepreneur Experience: ${data.creatorEntrepreneurExperience || 'Not provided'}
- Mewayz Growth Vision: ${data.mewayzGrowthVision || 'Not provided'}

RED FLAGS:
${flags.length > 0 ? flags.join('\n') : 'None identified'}

STRENGTHS:
${strengths.length > 0 ? strengths.join('\n') : 'Limited strengths identified'}

PREVIOUS COMPANIES/ACHIEVEMENTS:
${data.previousCompanies || 'Not provided'}

VALUE PROPOSITION:
${data.valueProposition || 'Not provided'}

NETWORK:
${data.network || 'Not provided'}

CHALLENGES PERSPECTIVE:
${data.challenges || 'Not provided'}

WHY NOW:
${data.whyNow || 'Not provided'}

RECOMMENDATION:
${score >= 70 ? '🟢 PROCEED - Strong candidate, schedule interview' : 
  score >= 40 ? '🟡 MAYBE - Decent candidate, ask follow-up questions' : 
  '🔴 SKIP - Not a good fit, politely decline'}

============================
Generated: ${thaiTime}
            `;
        }

        // Initialize with English when the page loads
        document.addEventListener('DOMContentLoaded', changeLanguage);
    </script>


</body>
</html>
</x-layouts.site>
