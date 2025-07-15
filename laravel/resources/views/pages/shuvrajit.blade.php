<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chief Business Development Officer Partnership Agreement</title>
    <!-- Load Tailwind CSS for professional styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load jsPDF for generating PDF (html2canvas will be loaded by jspdf.autotable or included if needed for specific elements) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <!-- html2canvas is typically included by jspdf's html method, but sometimes it's good to explicitly load if issues arise -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* Professional font for a formal document */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-size: 0.9375rem; /* Slightly smaller base font size for a professional print look (15px) */
            line-height: 1.6; /* Improved line height for readability */
        }
        /* Adjusting specific text elements relative to new base font size */
        h1 { font-size: 2.75rem; /* ~44px */ }
        h2 { font-size: 2.125rem; /* ~34px */ }
        h3 { font-size: 1.5rem; /* ~24px */ }
        .text-lg { font-size: 1.0625rem; /* ~17px */ }
        .text-xl { font-size: 1.25rem; /* ~20px */ }
        .text-sm { font-size: 0.8125rem; /* ~13px */ }

        /* Ensuring standard focus rings for accessibility, but keeping general look clean */
        button:focus, a:focus, input:focus, textarea:focus {
            outline: 2px solid rgba(59, 130, 246, 0.5); /* blue-500 with transparency */
            outline-offset: 2px;
        }
        /* Custom scrollbar for a polished user experience */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #e5e7eb; /* bg-gray-200 */
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: #9ca3af; /* bg-gray-400 */
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280; /* bg-gray-500 */
        }

        /* Signature canvas styling */
        #signatureCanvas {
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.5rem; /* rounded-md */
            background-color: #f9fafb; /* gray-50 */
            cursor: crosshair;
        }

        /* Classes for PDF generation control */
        .hide-on-pdf {
            display: none !important;
        }
        /* Ensure each major section starts on a new page */
        section {

        }
        /* Adjust for first section to not have page-break-before */
        body > div > #agreement-content > section:first-of-type {
            page-break-before: auto;
            break-before: auto;
        }
        /* Ensure each numbered subsection tries to stay together */
        section > div { /* This targets the direct children divs within a section, which hold the numbered content like 1.1, 1.2, etc. */
            page-break-inside: avoid; /* Prevents breaking inside the div content */
            break-inside: avoid; /* Modern CSS property */
            page-break-after: auto; /* Allows breaks after the div */
            break-after: auto; /* Modern CSS property */
            padding-bottom: 0.75rem; /* Small padding at the bottom of each subsection div */
        }
        section h2 { /* Ensure headings themselves don't break from their content */
            page-break-after: avoid;
            break-after: avoid;
            margin-top: 2rem; /* Add some space above section titles */
        }

        /* Further refine for list items in vesting schedule to avoid breaks inside */
        ul.list-disc.list-inside li {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 0.5rem; /* Add some space between list items */
        }
        /* Ensure signature section breaks correctly at the end */
        #signature-section {
            page-break-before: always;
            break-before: page;
        }

        /* Global max-width for content to simulate a more print-friendly column width */
        #agreement-content {
            max-width: 800px; /* Adjusted from max-w-7xl to a fixed px for consistent print width */
            padding: 2.5rem 3rem; /* Increase padding for more margin on print */
        }

        /* Adjustments for the signature images in print */
        .signature-image {
            width: 150px; /* Fixed width for consistent signature size */
            height: auto;
            display: block; /* Ensures it takes its own line */
            margin-top: 0.5rem; /* Space below the "Signature:" text */
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center py-12 px-4 sm:px-6 lg:px-8">
        <div id="agreement-content" class="w-full bg-white rounded-lg shadow-xl border-t-4 border-blue-700">

            <!-- Header Section: Title and Subtitle for official context -->
            <header class="text-center mb-10">
                <h1 class="font-extrabold text-blue-800 tracking-tight leading-tight mb-4 uppercase">
                    Chief Business Development Officer Partnership Agreement
                </h1>
                <p class="text-gray-600 text-xl font-medium">
                    Formalizing the Strategic Alliance between Mewayz Global Limited and Shuvrajit Bhaduri
                </p>
                <hr class="mt-8 border-gray-300 border-t-2 rounded-full mx-auto w-24">
            </header>

            <!-- Parties Section: Precise legal identification of contracting entities -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    Parties to the Agreement
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    This Agreement (hereinafter referred to as the "Contract" or "Instrument") is made and entered into by and between the following legally constituted entities and individuals:
                </p>

                <div class="bg-gray-50 p-6 rounded-md shadow-sm mb-6 border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-4">The Company:</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        <strong class="text-blue-700">Mewayz Global Limited</strong>, a private limited company duly incorporated, registered, and existing in good standing under the laws of <strong class="text-blue-600">Belgium</strong>, with its principal place of business and registered office located at <span class="text-blue-600">[Company Address - to be filled if applicable]</span>, including its successors, permitted assigns, and legal representatives (hereinafter referred to as "Mewayz" or the "Company").
                    </p>
                </div>

                <div class="bg-gray-50 p-6 rounded-md shadow-sm mb-6 border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-4">The Chief Business Development Officer:</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        <strong class="text-blue-700">Shuvrajit Bhaduri</strong>, an individual of legal age and capacity, holding citizenship of <strong class="text-blue-600">India</strong>, and currently residing in <strong class="text-blue-600">India</strong>, with the primary contact email address <strong class="text-blue-600">bhaduri.shuvrajit@gmail.com</strong>, including his heirs, legal representatives, and permitted assigns (hereinafter referred to as the "CBDO" or the "Chief Business Development Officer").
                    </p>
                </div>

                <p class="text-gray-500 italic text-sm mt-4 text-justify">
                    (Mewayz Global Limited and Shuvrajit Bhaduri are hereinafter collectively referred to as the "Parties" and individually as a "Party.")
                </p>
                <p class="text-gray-700 mt-6 text-justify">
                    This Agreement is entered into and shall become legally binding and fully effective as of <strong class="text-blue-700">[Date of Execution, e.g., June 25, 2025]</strong> (the "Effective Date"). This designated date unequivocally signifies the commencement of all rights, obligations, and covenants stipulated herein.
                </p>
            </section>

            <!-- Appointment and Role Section: Defining the scope and authority of the CBDO -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    1. Appointment and Scope of Role
                </h2>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">1.1. Position, Comprehensive Authority, and Reporting Structure</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        Mewayz Global Limited hereby formally appoints and engages Shuvrajit Bhaduri as its <strong class="text-blue-700">Chief Business Development Officer (CBDO)</strong>. In this critical, senior executive role, the CBDO shall possess comprehensive authority and direct responsibility for conceiving, developing, executing, and continuously overseeing all strategic business development initiatives, establishing and nurturing key long-term partnerships, and driving significant, measurable, and sustainable growth for Mewayz across its diverse product lines, service offerings, and target markets. The CBDO shall operate with a high degree of strategic and operational autonomy within the overarching strategic directives, approved budget allocations, and performance targets formally established by the Company's Board of Directors. The CBDO shall report directly and exclusively to the Chief Executive Officer (CEO)/Founder. This singular reporting line ensures direct accountability, streamlined decision-making, and profound strategic alignment at the highest echelon of the organization.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">1.2. Core Duties and Responsibilities</h3>
                    <p class="text-gray-700 mb-4 text-justify">
                        The CBDO explicitly covenants and agrees to dedicate their full professional time, diligent efforts, unwavering commitment, and extensive expertise to Mewayz, demonstrating the highest level of professional excellence, ethical conduct, and fiduciary responsibility. The CBDO shall perform the following core duties and responsibilities, which are deemed essential to the success and growth of Mewayz:
                    </p>

                    <ul class="list-disc list-inside space-y-4 text-gray-700 ml-4">
                        <li>
                            <strong class="text-blue-700">1.2.1. Strategic Partnerships and Global Alliances:</strong> The CBDO shall be responsible for identifying, evaluating, negotiating, and formalizing strategic partnerships and global alliances that align with Mewayz's long-term vision and growth objectives. This includes, but is not limited to:
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>A. Partnership Identification:</strong> Proactively identifying and prioritizing potential strategic partners that align with Mewayz's strategic vision and market expansion objectives.</li>
                                <li><strong>B. Partnership Development:</strong> Leading efforts to negotiate and execute mutually beneficial agreements with prospective partners, including technology companies, distribution channels, and other industry leaders, to drive initial value for Mewayz.</li>
                                <li><strong>C. Relationship Building:</strong> Building and nurturing early-stage relationships with key partners to establish a foundation for future strategic alignment and value generation.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">1.2.2. Web3 & Creator Economy Initiatives:</strong> The CBDO will contribute to Mewayz's strategic expansion within the Web3 and creator economy ecosystems. This encompasses:
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>A. Web3 Strategy Support:</strong> Contributing to the ideation and strategic planning for Mewayz's initial engagement with Web3 technologies, focusing on foundational steps for monetization strategies and community features.</li>
                                <li><strong>B. Ecosystem Exploration:</strong> Exploring potential relationships with relevant blockchain platforms, decentralized protocols, and other key players in the Web3 ecosystem to understand opportunities for Mewayz.</li>
                                <li><strong>C. Industry Awareness:</strong> Staying informed about the evolving regulatory landscape and technological advancements in Web3 and decentralized solutions.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">1.2.3. Investor Relations Support:</strong> The CBDO will play a supportive role in Mewayz's initial fundraising efforts and investor engagement, in close collaboration with the CEO/Founder and the finance team. This involves:
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>A. Investor Network Development:</strong> Assisting in identifying and establishing initial connections with potential angel investors and early-stage venture capitalists who align with Mewayz's vision.</li>
                                <li><strong>B. Pitch Material Input:</strong> Providing input and assistance in developing core presentation materials that highlight Mewayz's value proposition and early growth potential for potential investors.</li>
                                <li><strong>C. Due Diligence Assistance:</strong> Offering support with preliminary due diligence processes for initial funding discussions as required.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">1.2.4. Brand Awareness and Market Presence:</strong> Contributing to the strategic development of Mewayz's brand presence and initial market penetration through collaborations. This includes:
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>A. Brand Collaboration Identification:</strong> Identifying potential brand collaborations that can enhance Mewayz's early visibility and reach.</li>
                                <li><strong>B. Co-Marketing Exploration:</strong> Exploring initial co-marketing opportunities and basic joint initiatives that can introduce Mewayz to new audiences.</li>
                                <li><strong>C. Strategic Outreach:</strong> Supporting initial strategic outreach efforts to expand Mewayz's presence in relevant early markets.</li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Equity Compensation Structure Section: Detailed equity grant and vesting conditions -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    2. Equity Compensation Structure
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    This section defines the CBDO's equity compensation in Mewayz Global Limited, outlining the total grant and the vesting schedule. This equity compensation represents a long-term alignment of interests between the CBDO and the Company.
                </p>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">2.1. Total Equity Grant and Ownership Principles</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        The CBDO shall be irrevocably granted a total equity stake representing precisely <strong class="text-blue-700">six percent (6%)</strong> of the fully diluted share capital of Mewayz Global Limited. This percentage shall be calculated on a strict **post-money basis**, immediately prior to the closing of any future primary investment rounds, unless otherwise explicitly stipulated in definitive, subsequent investment agreements. This grant unequivocally represents direct beneficial ownership of Common Shares of Mewayz, imbued with full and standard shareholder rights, protections, and obligations as defined by the Company's current Articles of Association and any forthcoming, definitive Shareholders' Agreement. The Company represents and warrants that these shares, once vested, shall be duly authorized, validly issued, fully paid, and non-assessable.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">2.2. Equity Vesting Schedule</h3>
                    <p class="text-gray-700 mb-4 text-justify">
                        The total equity grant of 6% is subject to the following vesting schedule:
                    </p>

                    <ul class="list-disc list-inside space-y-6 text-gray-700 ml-4">
                        <li>
                            <strong class="text-blue-700">2.2.1. Immediate Vesting (2% of Company Equity)</strong>
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>Trigger Event:</strong> The formal and verifiable execution and mutual signing of this Partnership Agreement by both the duly authorized representative of Mewayz Global Limited and the CBDO.</li>
                                <li><strong>Vesting Amount:</strong> An equity stake representing an unconditional <strong class="text-blue-700">two percent (2%)</strong> of the Company's then-current fully diluted share capital shall vest immediately upon the Effective Date of this Agreement.</li>
                                <li><strong>Vesting Conditions:</strong> This specific portion of the equity (2%) is entirely unconditional and shall be irrevocably owned by the CBDO upon the precise moment of contract execution.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">2.2.2. Partnership Milestones Vesting (1% of Company Equity)</strong>
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>Trigger Event:</strong> Achievement of the "Initial Partnership Impact" milestone.</li>
                                <li><strong>Vesting Amount:</strong> An additional <strong class="text-blue-700">one percent (1%)</strong> equity stake shall vest upon the successful and verifiable completion of one of the following conditions, directly attributable to partnerships initiated by the CBDO:
                                    <ul class="list-disc list-inside ml-8 space-y-1 mt-1">
                                        <li>Generating <strong class="text-blue-700">USD $100,000</strong> in annual revenue.</li>
                                        <li>Acquiring <strong class="text-blue-700">10,000 new users</strong> (whether free or paid).</li>
                                    </ul>
                                </li>
                                <li><strong>Verification:</strong> Achievement of this milestone will be subject to verifiable data from Mewayz's tracking systems and formal approval by the Company's Board of Directors.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">2.2.3. Accelerated Partnership Milestones Vesting (1% of Company Equity)</strong>
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>Trigger Event:</strong> Achievement of the "Accelerated Partnership Impact" milestone.</li>
                                <li><strong>Vesting Amount:</strong> An additional <strong class="text-blue-700">one percent (1%)</strong> equity stake shall vest upon the successful and verifiable completion of one of the following conditions, directly attributable to partnerships initiated by the CBDO:
                                    <ul class="list-disc list-inside ml-8 space-y-1 mt-1">
                                        <li>Generating <strong class="text-blue-700">USD $500,000</strong> in annual revenue.</li>
                                        <li>Acquiring <strong class="text-blue-700">50,000 new users</strong> (whether free or paid).</li>
                                    </ul>
                                </li>
                                <li><strong>Verification:</strong> Achievement of this milestone will be subject to verifiable data from Mewayz's tracking systems and formal approval by the Company's Board of Directors. This milestone is cumulative with 2.2.2.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">2.2.4. Initial Funding Equity Vesting (1% of Company Equity)</strong>
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>Trigger Event:</strong> Direct, verifiable, and instrumental contribution by the CBDO to successful capital funding rounds.</li>
                                <li><strong>Vesting Amount:</strong> An additional <strong class="text-blue-700">one percent (1%)</strong> equity stake shall vest upon the Company successfully closing an external funding round of <strong class="text-blue-700">USD $100,000 or more</strong>, with the CBDO's material contribution to securing this funding formally acknowledged by the Company's Board of Directors.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">2.2.5. Major Funding Equity Vesting (1% of Company Equity)</strong>
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li><strong>Trigger Event:</strong> Direct, verifiable, and instrumental contribution by the CBDO to a major capital funding round.</li>
                                <li><strong>Vesting Amount:</strong> An additional <strong class="text-blue-700">one percent (1%)</strong> equity stake shall vest upon the Company successfully closing an external funding round of <strong class="text-blue-700">USD $500,000 or more</strong>, with the CBDO's material contribution to securing this funding formally acknowledged by the Company's Board of Directors. This is in addition to 2.2.4 if applicable.</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-blue-700">2.2.6. Bonus Compensation for Funding Rounds (Separate from Equity)</strong>
                            <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                                <li>For any funding round where the CBDO's direct contribution helps the Company raise <strong class="text-blue-700">USD $100,000 or more</strong>: The CBDO will receive a cash bonus equivalent to <strong class="text-blue-700">one percent (1%)</strong> of the total funds raised in that specific round.</li>
                                <li>For any funding round where the CBDO's direct contribution helps the Company raise <strong class="text-blue-700">USD $500,000 or more</strong>: The CBDO will receive a cash bonus equivalent to <strong class="text-blue-700">two percent (2%)</strong> of the total funds raised in that specific round. This supersedes the 1% bonus for that specific round.</li>
                                <li><strong>Example:</strong> If $100,000 is raised, the bonus is $1,000. If $489,000 is raised, the bonus is $4,890 (1%). If $500,000 is raised, the bonus is $10,000 (2%).</li>
                            </ul>
                        </li>
                        <li>
                            <strong class="text-red-700">2.2.7. Strict Confidentiality and Clawback Clause for Bonuses and Equity:</strong> The bonus compensation and all aspects of this equity package are to be held in the strictest confidence by the CBDO. This information **MUST NOT** be disclosed to any other team members, employees, contractors, or any external third parties, with the sole exception of the CBDO's spouse, provided the spouse is also bound by an equivalent confidentiality obligation. Any unauthorized disclosure of this compensation information that demonstrably and materially harms the Company's reputation, internal morale, or financial standing, as determined solely by the Company's Board of Directors, will result in immediate termination of this Agreement for Cause. In such an event, Mewayz Global Limited reserves the absolute and unequivocal right to **claim back all equity previously vested to the CBDO (including the immediate 2% vesting) and demand repayment of all bonuses** paid under this section, without any costs incurred by Mewayz.
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Legal Framework and Governance Section: Core legal provisions -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    3. Legal Framework and Corporate Governance
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    This section meticulously delineates the fundamental legal and corporate governance principles governing the CBDO's equity ownership, intellectual property rights, confidentiality obligations, and general conduct within the partnership, ensuring absolute clarity, mutual understanding, and legal enforceability.
                </p>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">3.1. Equity Structure, Shareholder Rights, and Corporate Provisions</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Ownership Type and Class:</strong> The CBDO shall hold direct beneficial and legal equity ownership in Mewayz Global Limited, specifically in the form of fully paid, non-assessable <strong class="text-blue-700">Common Shares</strong>. These shares shall be issued from Mewayz's authorized share capital and shall rank pari passu with all other issued and outstanding common shares of the Company.</li>
                        <li><strong>B. Standard Shareholder Rights:</strong> These Common Shares shall carry all standard rights as defined by Mewayz's current Articles of Association (or Memorandum and Articles of Association) and any future amendments duly approved by the shareholders. These rights include, but are not limited to, pro-rata rights to dividends (if and when formally declared by the Board of Directors), and pro-rata participation in the distribution of assets upon any liquidation or dissolution of the Company, after any preferred shareholders have received their liquidation preferences.</li>
                        <li><strong>C. Voting Rights:</strong> The CBDO's duly vested shares shall carry full voting rights, precisely proportionate to their ownership percentage of the total issued and outstanding voting shares of the Company, on all matters legally brought before the shareholders for approval (e.g., election of directors, major corporate actions such as mergers or acquisitions, share issuances, amendments to foundational corporate documents).</li>
                        <li><strong>D. Transfer Restrictions and Shareholder Agreements:</strong> The CBDO's ability to transfer, assign, pledge, encumber, or otherwise dispose of their shares will be strictly subject to standard corporate transfer restrictions, designed to maintain Company control, protect shareholder interests, and preserve the Company's equity structure. These typically include, but are not limited to: (a) a mandatory **Right of First Refusal (ROFR)**, giving Mewayz or other existing shareholders the first option to purchase the shares at the same terms offered by a bona fide third-party purchaser; (b) **Co-Sale (Tag-Along) Agreements**, allowing the CBDO to sell a proportionate number of their shares if a significant shareholder (e.g., founder, major investor) sells a controlling interest to a third party; (c) potential **Drag-Along Rights**, whereby the CBDO may be required to sell their shares if a majority of shareholders (as defined in relevant agreements) agree to a sale of the Company to a third-party acquirer; and (d) any other restrictions explicitly outlined in a separate, definitive **Shareholders' Agreement** which shall be executed concurrently with or shortly after this Agreement, and which shall govern the detailed rights, obligations, and relationships of all shareholders and may include specific terms regarding vesting, forfeiture, and repurchase of shares.</li>
                        <li><strong>E. Dilution Protection:</strong> The CBDO shall have **pro-rata participation rights** in future primary funding rounds (e.g., equity or convertible debt offerings), allowing them the option, but not the obligation, to invest an amount proportionate to their then-current percentage ownership to maintain their precise percentage stake, thereby protecting against future dilution. This right is subject to the terms and conditions of such future funding rounds, and any specific terms in the Shareholders' Agreement or Subscription Agreement.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">3.2. Non-Disclosure and Perpetual Confidentiality Obligations</h3>
                    <p class="text-gray-700 leading-relaxed mb-2 text-justify">
                        The CBDO explicitly acknowledges and unequivocally agrees that during the entire course of their engagement as CBDO, they will have extensive access to and be privy to highly sensitive, proprietary, and confidential information belonging to Mewayz, its affiliates, its existing and prospective partners, and its clients. The CBDO explicitly covenants to maintain the strictest confidence regarding all such non-public, confidential, and proprietary information. This obligation shall apply to, but is not limited to, the following categories of information:
                    </p>
                    <ul class="list-disc list-inside ml-8 space-y-2 mt-2">
                        <li><strong>A. Company Financial Information and Business Plans:</strong> All internal financial data (including current and historical revenues, costs, profit margins, detailed projections, budgets, funding details, capitalization tables), strategic business plans, marketing plans, sales forecasts, and operational methodologies.</li>
                        <li><strong>B. Partnership Negotiations and Strategic Discussions:</strong> The specific details, terms, ongoing status, and internal strategic discussions related to all current, contemplated, or potential partnership negotiations, including proposals, definitive agreements, and sensitive contact information.</li>
                        <li><strong>C. Technology Roadmaps and Product Development Plans:</strong> Information concerning Mewayz's current and future technological developments, software architecture, algorithms, trade secrets, proprietary code, product features, user interface designs, and underlying intellectual property.</li>
                        <li><strong>D. Customer and User Data and Business Intelligence:</strong> Any non-public data pertaining to Mewayz's customers, users, vendors, suppliers, internal market research, proprietary business insights, analytics, and operational data, including personal data subject to privacy laws.</li>
                        <li><strong>E. All Proprietary Information and Trade Secrets:</strong> Any other information, data, processes, formulae, know-how, methods, or strategies considered confidential and proprietary to Mewayz, whether or not explicitly marked as confidential, and whether or not in tangible form (e.g., oral disclosures).</li>
                    </ul>
                    <p class="text-gray-600 italic text-sm mt-4 text-justify">
                        <strong class="text-blue-700">Duration and Survival:</strong> These confidentiality obligations are fundamental to Mewayz's business and shall be **perpetual**, surviving the termination of this Agreement indefinitely, regardless of the reason for termination. The CBDO further covenants to immediately return or securely destroy all confidential materials (physical or digital copies) upon termination or at any time upon Mewayz's written request. In the event the CBDO is legally compelled by a court order or subpoena to disclose confidential information, the CBDO shall, to the extent legally permissible, provide Mewayz with prompt prior written notice to allow Mewayz to seek a protective order or other appropriate legal remedy.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">3.3. Intellectual Property Protection and Comprehensive Assignment</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Company IP Ownership and Works Made for Hire:</strong> All intellectual property (IP), including but not limited to inventions (whether patentable or not), discoveries, designs, software code, applications, methodologies, processes, data models, algorithms, trademarks, service marks, copyrights, trade secrets, know-how, improvements, and all works of authorship, that are conceived, made, developed, reduced to practice, or otherwise created by the CBDO, either solely or jointly with others, directly or indirectly, in connection with their role and duties for Mewayz, or that relate to Mewayz's business, current activities, or demonstrably benefit Mewayz, during the term of this Agreement, shall be the **sole, exclusive, and absolute property of Mewayz Global Limited**. The CBDO expressly agrees that all such creations shall be deemed "Works Made For Hire" to the fullest extent permitted under applicable copyright laws, and the CBDO irrevocably waives all moral rights in such works.</li>
                        <li><strong>B. CBDO Pre-Existing IP:</strong> The CBDO explicitly retains ownership of any intellectual property developed independently prior to the Effective Date of this Agreement, provided such IP is clearly documented and fully disclosed to Mewayz in writing upon the commencement of this Agreement, or upon its creation if after the Effective Date but before commencement of work requiring its use. The CBDO also acknowledges that their personal professional network relationships, developed prior to this Agreement, are their own, provided they are not leveraged in a manner that directly competes with Mewayz's core business interests or violates confidentiality obligations herein.</li>
                        <li><strong>C. Invention Assignment Covenant:</strong> The CBDO hereby irrevocably assigns, transfers, and conveys to Mewayz, effective immediately upon creation, all rights, title, and interest in and to any and all work-related inventions, improvements, discoveries, innovations, and intellectual property (as broadly defined above) conceived, made, or reduced to practice during the term of this Agreement and that relate to Mewayz's business, current activities, or demonstrably benefit Mewayz. The CBDO expressly agrees to cooperate fully with Mewayz, both during and after the term of this Agreement (at Mewayz's sole expense for any post-termination cooperation), to perfect Mewayz's rights in such IP, including without limitation, executing all necessary documents (e.g., patent applications, copyright registrations), assisting in litigation, and providing reasonable assistance as may be required.</li>
                        <li><strong class="text-blue-700">D. Non-Compete and Non-Solicitation Covenants:</strong> To protect Mewayz's legitimate business interests, confidential information, trade secrets, and invaluable customer, CBDO, and employee relationships, the CBDO explicitly agrees to the following covenants:
                            <ul class="list-disc list-inside ml-8 space-y-1 mt-1">
                                <li><strong>i. Non-Compete:</strong> During the entire term of this Partnership Agreement and for a period of <strong class="text-blue-700">twelve (12) months immediately following the effective date of its termination</strong> (regardless of the reason for termination), the CBDO expressly covenants not to directly or indirectly engage in, manage, operate, own (other than passive investments of less than one percent (1%) in publicly traded companies), be employed by, consult for, or hold any interest in any business, organization, or venture that directly competes with Mewayz's core business operations (defined as the development and provision of creator economy platforms, Web3 monetization solutions, and strategic business development services specifically for creators and brands) within any geographic market where Mewayz actively conducts business, operates, or has verifiable, documented plans to conduct business (as evidenced by formal business plans or Board resolutions).</li>
                                <li><strong>ii. Non-Solicitation of CBDOs/Clients:</strong> For a period of <strong class="text-blue-700">twelve (12) months immediately following the effective date of termination</strong>, the CBDO covenants not to directly or indirectly solicit, induce, or attempt to induce any of Mewayz's then-current or prospective CBDOs or key clients with whom the CBDO had direct contact, material influence, or access to confidential information during the term of this Agreement, to terminate or reduce their business relationship with Mewayz or to enter into a business relationship with any third party that directly competes with Mewayz.</li>
                                <li><strong>iii. Non-Solicitation of Employees:</strong> For a period of <strong class="text-blue-700">twelve (12) months immediately following the effective date of termination</strong>, the CBDO covenants not to directly or indirectly solicit, induce, or attempt to induce any employee, contractor, or consultant of Mewayz to leave their engagement with Mewayz or to accept engagement with any third party.</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">3.4. Partnership Agreement Protections and Recourse Mechanisms</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Board Representation/Observer Rights:</strong> The CBDO shall be entitled to receive timely advance written notice of, and attend as a non-voting observer, all regular and special meetings of the Board of Directors of Mewayz Global Limited where matters directly affecting the Company's strategic partnership initiatives, the CBDO's role, the Company's Web3 strategy, significant financial performance metrics, or their equity vesting are formally discussed. This right is intended to provide critical strategic insight and transparency but does not, in itself, confer voting power unless the CBDO is subsequently and formally elected or appointed as a voting member of the Board.</li>
                        <li><strong>B. Information Rights and Transparency:</strong> To ensure full transparency and enable the CBDO to effectively monitor performance and impact, the CBDO shall receive comprehensive monthly financial reports (including profit & loss, balance sheet, cash flow statements), detailed quarterly partnership impact assessments, and annual audited financial statements (when available) of Mewayz. These reports shall provide sufficient detail, in accordance with GAAP/IFRS, to ascertain the Company's overall financial health and the direct financial and strategic impact of the CBDO's work.</li>
                        <li><strong>C. Approval Rights for Key Decisions:</strong> The CBDO's formal written approval (or explicit acknowledgment and non-objection, as agreed by the Board for certain operational decisions) shall be explicitly required for any major strategic partnership decisions that directly and materially affect the calculation of their equity vesting as outlined in Section 2.2. This ensures alignment and protects the CBDO's vested interests and strategic contributions.</li>
                        <li><strong>D. Dispute Resolution Process:</strong> Any and all disputes, controversies, or claims arising from or relating to this Partnership Agreement, its interpretation, formation, breach, termination, or validity, shall be exclusively and mandatorily resolved through the tiered, formal dispute resolution process meticulously outlined in Section 6.1 of this Agreement, which shall be the sole and exclusive forum for such disputes.</li>
                    </ul>
                </div>
            </section>

            <!-- Communication and Collaboration Framework Section: Defining operational interactions -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    4. Communication and Collaboration Framework
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    Effective, transparent, and consistent communication, coupled with robust inter-departmental collaboration, are paramount for the CBDO's success as CBDO and for the integrated, sustainable growth of Mewayz. This section outlines the official protocols and expectations for internal and external interactions.
                </p>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">4.1. Primary Communication Channels and Established Cadence</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Primary Real-Time Platform:</strong> <strong class="text-blue-700">Slack</strong> (or an equivalent Board-approved enterprise communication platform) shall serve as the primary, real-time platform for daily internal communications, urgent project updates, quick queries, and informal team collaboration. Specific, dedicated channels will be established for each major partnership initiative, cross-functional team discussions, and high-priority alerts. Expected response time for critical messages on Slack is within 2 hours during normal business hours (9:00 AM - 6:00 PM local time).</li>
                        <li><strong>B. Formal Written Communication:</strong> Email will be exclusively utilized for all formal communications, official external CBDO correspondence, sharing of legal documents, sensitive or confidential information, long-form strategic updates, and meeting minutes requiring formal record-keeping. All such emails must be archived according to Mewayz's data retention policy. Expected response time for formal emails is within 24 business hours.</li>
                        <li><strong>C. Video Conferencing and Structured Meetings:</strong> Regular weekly strategy sessions (e.g., Monday Kickoff, Friday Review), comprehensive monthly partnership review meetings, and bi-weekly one-on-one sessions with the CEO will be consistently conducted via secure video conferencing platforms (e.g., Google Meet, Zoom, Microsoft Teams) to ensure face-to-face interaction, in-depth discussions, and collaborative problem-solving. Ad-hoc video calls will be scheduled as needed for urgent matters requiring immediate discussion and resolution.</li>
                        <li><strong>D. Integrated Project Management System:</strong> A designated integrated project tracking system (e.g., Asana, Jira, Trello, or an equivalent enterprise-grade solution) will be consistently utilized for managing all partnership initiatives. This system will be used for tracking progress on specific deliverables, assigning tasks and responsibilities, monitoring key milestones, and documenting communication threads related to projects in a transparent manner accessible to all relevant internal teams. All key tasks, deadlines, and associated documents related to partnerships must be logged and maintained in this system.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">4.2. Reporting Structure and Inter-Departmental Collaboration</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Direct Reporting Line:</strong> The CBDO shall have a direct and primary reporting line to the Chief Executive Officer (CEO)/Founder of Mewayz Global Limited. This direct line ensures strategic alignment, efficient decision-making, and direct accountability for the Company's business development and partnership growth.</li>
                        <li><strong>B. Matrix Relationship with Core Teams:</strong> The CBDO will actively maintain a crucial and dynamic matrix relationship with the product development, engineering, and technical teams. This ensures that all proposed partnership strategies are technically feasible, align seamlessly with the existing product roadmap and future development plans, and can be efficiently implemented and supported post-integration. This includes mandatory regular sync meetings with product managers, lead engineers, and technology leadership to ensure technical compatibility, resource allocation, and address any potential technical dependencies or constraints.</li>
                        <li><strong>C. Cross-Functional Team Collaboration:</strong> The CBDO will actively engage in, lead, or strategically support cross-functional collaboration with other key internal departments. This includes, but is not limited to, the marketing team (for integrated co-marketing campaigns and brand alignment), sales team (for lead generation, sales enablement through CBDO channels, and joint sales initiatives), legal team (for contract review, compliance, and risk mitigation), finance team (for budgeting and financial reporting), and customer success team (for CBDO onboarding, ongoing support, and issue resolution), ensuring seamless execution and integrated business growth.</li>
                        <li><strong>D. External Stakeholder Relationships:</strong> The CBDO will hold primary responsibility for the direct management, cultivation, and strategic expansion of all external CBDO relationships, key client relationships related to partnerships, and strategic investor communications pertaining to their role. The CBDO shall act as the principal point of contact and authorized Company representative in these high-level engagements, ensuring consistency in messaging and alignment with Mewayz's brand guidelines.</li>
                        <li><strong>E. Board and Executive Interaction:</strong> The CBDO will be expected to prepare and deliver comprehensive quarterly presentations to the Board of Directors and the Executive Team on overall partnership strategy, detailed performance metrics, key achievements, challenges encountered, and future growth opportunities, providing actionable insights, strategic recommendations, and participating actively in executive-level strategic planning.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">4.3. Decision-Making Authority and Financial Accountability</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Partnership Negotiations and Terms:</strong> The CBDO shall possess full and autonomous authority to negotiate the commercial, legal, and operational terms of partnership agreements within the pre-approved strategic framework, business development objectives, and allocated annual budget. Any significant deviations from the established strategic framework or the agreed-upon budget for a specific partnership (e.g., changes to equity implications, non-standard legal clauses) require prior written approval from the CEO or the Board, as appropriate.</li>
                        <li><strong class="text-blue-700">B. Operational Budget Approval Authority:</strong> The CBDO shall have the authority to approve partnership-related operational expenses up to <strong class="text-blue-700">$25,000 USD monthly</strong> without requiring additional specific, per-transaction approval from the CEO or CFO, provided such expenditures cumulatively remain within the overall approved annual business development budget. Any single expenditure exceeding $25,000 USD, or total monthly expenditures that would cause the overall monthly budget to be exceeded, will require prior written approval from the CEO or CFO. The CBDO is responsible for strict adherence to approved budgets.</li>
                        <li><strong>C. Strategic Decisions and Initiatives:</strong> Major strategic initiatives concerning partnerships that significantly impact Mewayz's core business model, fundamental product direction, require substantial, unbudgeted resource allocation, or involve significant financial commitments will involve collaborative decision-making with the entire executive team (CEO, CTO, CFO) and, where appropriate and legally required, necessitate formal Board approval via a duly recorded resolution.</li>
                        <li><strong>D. Vendor and Service Provider Selection:</strong> The CBDO shall have independent authority for the identification, evaluation, and selection of third-party vendors and service providers specifically related to partnership development and execution (e.g., specialized market research tools, advanced partnership management software, external legal review for specific complex contracts, industry event sponsorships). All such selections must be within approved budget limits and in strict adherence to Mewayz's established procurement policies, vendor due diligence processes, and legal guidelines.</li>
                    </ul>
                </div>
            </section>

            <!-- Termination and Transition Provisions Section: Comprehensive exit clauses -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    5. Termination and Professional Transition Provisions
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    This section comprehensively outlines the conditions under which this Agreement may be terminated by either Party, the precise implications for equity vesting, and the meticulous procedures for a smooth, orderly, and professional transition of responsibilities, knowledge, and assets upon cessation of the partnership.
                </p>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">5.1. Termination Scenarios and Stipulated Notice Periods</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Voluntary Termination by CBDO (Resignation):</strong> The CBDO may terminate this Agreement for any reason by providing Mewayz with <strong class="text-blue-700">sixty (60) calendar days' prior written notice</strong>, delivered to the CEO and Company Secretary. During this notice period, the CBDO is explicitly expected and legally obligated to continue fulfilling all duties and responsibilities diligently, maintain strict confidentiality, and actively assist in the comprehensive transition process to ensure minimal disruption to Mewayz's ongoing operations and partnership relationships.</li>
                        <li><strong class="text-blue-700">B. Termination for Cause by Company:</strong> Mewayz may terminate this Agreement immediately (without any prior notice period, severance payment, or further vesting of unvested equity) for "Cause." "Cause" shall be broadly defined and includes, but is not limited to, the CBDO's: (a) a material and uncured breach of any significant provision of this Agreement, Mewayz's formal policies, or a written Company directive that remains uncured after <strong class="text-blue-700">fifteen (15) calendar days' written notice</strong> outlining the specific nature of the breach; (b) engagement in gross misconduct, fraud, dishonesty, embezzlement, or criminal acts detrimental to Mewayz's reputation, financial standing, or business operations; (c) repeated, willful, or persistent failure to perform assigned duties and responsibilities in a competent or timely manner, after receiving prior written warnings and a reasonable opportunity to cure (not exceeding 30 days); (d) a material violation of Mewayz's core ethical policies, codes of conduct, or any applicable laws and regulations; (e) any act of serious insubordination or significant, undisclosed conflict of interest that demonstrably harms Mewayz's business or reputation; or (f) any material misrepresentation or omission in their background, qualifications, or credentials provided to Mewayz prior to or during the engagement.</li>
                        <li><strong class="text-blue-700">C. Termination without Cause by Company:</strong> Mewayz may terminate this Agreement at any time without "Cause" by providing the CBDO with <strong class="text-blue-700">ninety (90) calendar days' prior written notice</strong>, delivered to the CBDO's last known address. In such an event, and as consideration for the early termination, Mewayz shall provide a severance payment equivalent to <strong class="text-blue-700">three (3) months</strong> of the CBDO's last agreed-upon base remuneration (if any salary was being drawn, calculated at the rate immediately prior to notice of termination) or a mutually agreed-upon cash equivalent compensation for equity-only roles, calculated based on the fair market value of the vested equity at the time of termination. This severance shall be payable in equal monthly installments over the three-month period following the effective date of termination, contingent upon the CBDO's full cooperation with the transition and ongoing adherence to all post-termination covenants (e.g., confidentiality, non-compete, non-solicitation).</li>
                        <li><strong>D. Termination due to Disability or Incapacity:</strong> In the event of the CBDO's temporary or permanent disability or prolonged incapacity that substantially prevents them from performing their core duties and responsibilities for a continuous period exceeding ninety (90) days, the Parties shall engage in good faith discussions to agree on modified terms for continued partnership (e.g., reduced duties, temporary replacement, remote work options) or a mutually agreeable termination plan. This may include potential adjusted equity vesting schedules or a partial acceleration as deemed fair and equitable by the Board, taking into account the CBDO's contributions up to that point.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">5.2. Equity Vesting Principles and Acceleration Upon Termination</h3>
                    <p class="text-gray-700 mb-2 text-justify">
                        The vesting and forfeiture of the CBDO's equity stake upon termination of this Agreement shall be strictly governed by the following precise provisions, ensuring fairness and adherence to the time-based structure:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Immediate Vesting Portion (Retained):</strong> The **two percent (2%) equity** that vested immediately upon the execution of this Agreement (as per Section 2.2.1) shall be unconditionally and irrevocably retained by the CBDO, regardless of the reason for termination.</li>
                        <li><strong>B. Vested Equity (Retained):</strong> Any additional equity portions that have demonstrably and verifiably vested according to the schedule (Sections 2.2.2, 2.2.3, 2.2.4, 2.2.5) **prior to the effective date of termination** shall be fully and irrevocably retained by the CBDO.</li>
                        <li><strong class="text-blue-700">C. Unvested Equity Forfeiture:</strong> Any equity that has not yet vested according to the vesting schedule at the time of the effective date of termination shall be **immediately and automatically forfeited** by the CBDO and shall revert to Mewayz Global Limited, without any further consideration or compensation to the CBDO, unless specific acceleration provisions outlined below explicitly apply. The CBDO shall have no further claim or interest in such unvested shares.</li>
                        <li><strong>D. Specific Acceleration Provisions:</strong>
                            <ul class="list-disc list-inside ml-8 space-y-1 mt-1">
                                <li><strong>i. Partial Acceleration for Termination without Cause:</strong> If Mewayz terminates the Agreement without "Cause" (as defined in Section 5.1), any unvested equity may be subject to a partial acceleration. The extent of such acceleration, if any, will be determined by the Board of Directors in good faith, taking into account the CBDO's contributions and the elapsed time towards the next vesting milestone.</li>
                                <li><strong>ii. Full Acceleration for Constructive Dismissal:</strong> In the event of a "constructive dismissal" (defined as a material, adverse, and non-consented-to change to the CBDO's primary role, core duties, level of authority, or a significant reduction in compensation, initiated by Mewayz without "Cause," leading directly to the CBDO's resignation within 30 days of such change), the CBDO may be entitled to an accelerated vesting of a portion or all of the unvested equity. The specific terms and extent of such acceleration shall be determined exclusively through the binding dispute resolution process outlined in Section 6.1.</li>
                                <li><strong>iii. Change of Control Acceleration:</strong> In the event of a Change of Control of Mewayz Global Limited (defined as the acquisition of 50% or more of the Company's voting shares or substantially all of its assets by a third party, or a merger where Mewayz is not the surviving entity), all remaining unvested equity of the CBDO shall immediately **accelerate and vest in full** immediately prior to the closing of such Change of Control transaction.</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">5.3. Comprehensive Transition and Handover Requirements Post-Termination</h3>
                    <p class="text-gray-700 mb-2 text-justify">
                        Upon termination of this Agreement for any reason, the CBDO shall fully cooperate with Mewayz to facilitate a smooth, orderly, and professional transition of responsibilities, knowledge, and assets. This includes, but is not limited to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Partnership Relationships Handover:</strong> The CBDO shall actively participate in a comprehensive <strong class="text-blue-700">ninety (90)-day transition period</strong> (or a shorter period, if mutually agreed upon for "for cause" termination or otherwise) commencing on the effective date of termination. During this period, the CBDO shall ensure a complete and effective handover of all existing, prospective, and pending partnership relationships, including making proper, professional introductions to Mewayz's designated replacements, providing detailed context on each relationship, and assisting with transfer of all relevant communications, contact information, and files.</li>
                        <li><strong>B. Documentation and Knowledge Transfer:</strong> The CBDO shall promptly provide complete, organized, and up-to-date documentation of all partnership negotiations, executed agreements, key contact details, detailed pipeline status, strategic insights, market intelligence, and all relevant project files, digital assets, and historical communications. This extensive knowledge transfer is crucial for business continuity and efficient onboarding of successors.</li>
                        <li><strong class="text-blue-700">C. Post-Termination Non-Solicitation of Associates and Clients:</strong> For a period of <strong class="text-blue-700">twelve (12) months immediately following the effective date of termination</strong> (regardless of the reason for termination), the CBDO expressly agrees not to directly or indirectly solicit for employment, engage as a contractor or consultant, or attempt to induce to leave Mewayz's employ, any of Mewayz's then-current employees, consultants, or independent contractors with whom the CBDO had direct contact or material influence during the term of this Agreement. Furthermore, the CBDO agrees not to directly or indirectly solicit, induce, or attempt to induce any of Mewayz's then-current or prospective CBDOs, key clients, or customers with whom the CBDO had direct contact or confidential information during the term of this Agreement, to terminate or reduce their business relationship with Mewayz or to enter into a business relationship with any third party that directly competes with Mewayz.</li>
                        <li><strong>D. Return of Company Property:</strong> Upon the effective date of termination, the CBDO shall immediately return to Mewayz all Company property, equipment, and materials, including but not limited to, laptops, mobile phones, access cards, identification badges, documents, data, software, and any other materials (physical or digital) belonging to Mewayz, its CBDOs, or clients, which are in the CBDO's possession or control.</li>
                        <li><strong>E. Client Relations and Reputation Protection:</strong> The CBDO shall ensure a professional, respectful, and ethical transition of all external relationships established during their tenure. The CBDO specifically covenants to refrain from any conduct that could disparage, harm, or negatively impact Mewayz's business interests, financial standing, reputation, or relationships with its CBDOs and clients, both during and after the termination of this Agreement.</li>
                    </ul>
                </div>
            </section>

            <!-- Dispute Resolution and Legal Provisions Section: Ensuring clear legal pathways -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    6. Dispute Resolution and General Legal Provisions
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    This section governs the mandatory and exclusive resolution of any disputes arising from this Agreement and includes other standard, yet critical, legal clauses ensuring the comprehensive enforceability and interpretation of the contract.
                </p>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">6.1. Mandatory Dispute Resolution Process</h3>
                    <p class="text-gray-700 mb-2 text-justify">
                        Any dispute, controversy, claim, or difference whatsoever arising out of or relating to this Agreement, its interpretation, formation, breach, termination, validity, or enforceability, shall be exclusively and mandatorily resolved through the following tiered and escalating process, which shall be the sole and exclusive forum for such disputes:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Tier 1: Internal Resolution (Good Faith Negotiation):</strong> The Parties shall first endeavor to resolve the dispute amicably through good faith, direct negotiation. This negotiation shall involve the CBDO and a senior executive of Mewayz (initially the CEO, or a designated representative). This negotiation period shall last for a minimum of <strong class="text-blue-700">thirty (30) calendar days'</strong> from the date one Party provides formal written notice of a dispute to the other Party, clearly outlining the nature of the dispute and the relief sought.</li>
                        <li><strong>B. Tier 2: Non-Binding Mediation:</strong> If the dispute remains unresolved after the internal negotiation period, the Parties agree to engage in non-binding mediation. The mediation shall be conducted by a single, impartial, and mutually selected mediator from a reputable mediation service provider in <strong class="text-blue-600">Brussels, Belgium</strong>. The costs of mediation shall be shared equally by both Parties. This mediation phase shall not exceed sixty (60) days unless mutually extended by written agreement of both Parties.</li>
                        <li><strong class="text-blue-700">C. Tier 3: Binding Arbitration (Exclusive Forum):</strong> If mediation is unsuccessful in resolving the dispute within sixty (60) days of the mediator's appointment (or extended period), the dispute shall be finally and exclusively settled by <strong class="text-blue-700">binding arbitration</strong>. The arbitration shall be administered by <strong class="text-blue-600">[Specify Reputable Arbitration Body, e.g., the Belgian Centre for Arbitration and Mediation (CEPANI) or the International Court of Arbitration of the ICC]</strong> under its <strong class="text-blue-600">[Specify Applicable Rules, e.g., CEPANI Rules or ICC Rules]</strong> then in force. The arbitration shall be conducted by a single arbitrator (or three arbitrators if the aggregate claim value exceeds $1,000,000 USD, or as otherwise required by the rules), who shall be agreed upon by the Parties within 15 days of the arbitration request, or, failing agreement, appointed by the designated arbitration body. The seat of arbitration shall be <strong class="text-blue-600">Brussels, Belgium</strong>, and the proceedings shall be conducted entirely in the <strong class="text-blue-700">English language</strong>. The arbitral award rendered by the arbitrator(s) shall be final and binding upon both Parties and may be entered as a judgment in any court of competent jurisdiction.</li>
                        <li><strong>D. Injunctive Relief Exception:</strong> Notwithstanding the above mandatory dispute resolution provisions, either Party may, at any time, seek immediate temporary or permanent injunctive relief or specific performance from a court of competent jurisdiction to prevent irreparable harm (e.g., in cases involving actual or threatened breach of confidentiality, intellectual property rights, non-compete, or non-solicitation covenants) while the tiered dispute resolution process is ongoing. Such a request for injunctive relief shall not be deemed a waiver of the obligation to pursue other disputes through the specified arbitration process.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">6.2. Force Majeure and External Circumstances Adjustment</h3>
                    <p class="text-gray-700 mb-2 text-justify">
                        Neither Party shall be held liable for any delay or failure to perform its obligations under this Agreement (excluding payment obligations, which remain absolute and unconditional) if and to the extent such delay or failure is caused by an "Event of Force Majeure." An "Event of Force Majeure" means any event beyond the reasonable control of the affected Party, including but not limited to acts of God, natural disasters (e.g., earthquakes, floods, pandemics, widespread disease outbreaks), acts of war (declared or undeclared), terrorism, civil unrest, widespread internet outages, governmental orders or regulations, or declared public health emergencies, that materially and directly prevent the performance of obligations.
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Performance Delays and Vesting Extensions:</strong> If the CBDO's ability to achieve specific vesting conditions is significantly delayed or hindered directly due to an Event of Force Majeure, the relevant timelines or performance periods may be <strong class="text-blue-700">extended by mutual written agreement</strong> of both Parties. This extension shall be reasonable and proportionate to the duration and demonstrable impact of the Force Majeure event, without imposing penalty to the CBDO for such excusable delays. The affected Party shall provide prompt written notice of the Force Majeure event.</li>
                        <li><strong>B. Material Adverse Market Conditions Adjustments:</strong> In the event of significant, unforeseen, and demonstrable adverse market disruptions (e.g., severe global economic downturn, industry-wide regulatory crackdown on Web3 or specific technologies, or major market collapse directly impacting Mewayz's business model) that materially and demonstrably impact the overall market's ability to achieve target revenue or user acquisition volumes, the time-based vesting schedule may be subject to **reasonable, good-faith adjustment** by mutual written agreement and formal Board approval. Such adjustments must be supported by verifiable independent market data and economic analysis.</li>
                        <li><strong>C. Regulatory Changes Adaption:</strong> Given the dynamic and rapidly evolving nature of the Web3, blockchain, and cryptocurrency regulatory landscape, explicit adaptation of Web3 and crypto-related duties may be mutually agreed upon by the Parties if significant new regulatory developments materially impact the feasibility, legality, or commercial viability of certain initiatives outlined in Section 1.2.2. Such adaptations shall aim to preserve the original intent and economic balance of the Agreement.</li>
                        <li><strong>D. Pandemic and Emergency Protocols:</strong> In extraordinary circumstances such as a declared global pandemic or other major public health emergency, modified performance expectations, timelines, and operational adjustments will be mutually agreed upon to reflect the changed operational environment and ensure the CBDO can continue to contribute effectively, safely, and in alignment with Mewayz's adapted business continuity plans.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">6.3. Amendment, Modification, and Waiver Procedures</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Exclusive Written Amendments:</strong> Any and all modifications, amendments, alterations, or waivers to this Agreement, including any appendices, schedules, or exhibits referenced herein, must be made <strong class="text-blue-700">exclusively in writing</strong> and be formally **signed by both Parties** (the CBDO and a duly authorized representative of Mewayz Global Limited) to be legally binding and effective. For the avoidance of doubt, no oral modifications, implied waivers, or conduct-based alterations shall be valid or enforceable.</li>
                        <li><strong>B. Board Approval for Material Changes:</strong> Any material changes to the core terms of this Agreement, especially those directly affecting the CBDO's equity vesting schedule, significant duties, reporting structure, compensation, or the Company's overall strategic direction, will require formal <strong class="text-blue-700">Board of Directors approval</strong> for Mewayz Global Limited, duly recorded in official Board minutes.</li>
                        <li><strong>C. Independent Legal Review:</strong> Both Parties acknowledge and strongly recommend that all proposed amendments or modifications to this Agreement be subject to independent legal review by their respective legal counsel to ensure full understanding and protection of their interests and compliance with applicable laws.</li>
                        <li><strong>D. Notice Requirements for Modifications:</strong> A <strong class="text-blue-700">thirty (30)-day advance written notice</strong> shall be provided by the proposing Party for any proposed material modifications to this Agreement, accompanied by a detailed impact analysis on the CBDO's role, responsibilities, and equity terms, before such modifications are presented for formal approval.</li>
                        <li><strong>E. No Waiver of Rights:</strong> No failure or delay by either Party in exercising any right, power, or privilege under this Agreement shall operate as a waiver thereof, nor shall any single or partial exercise thereof preclude any other or further exercise thereof or the exercise of any other right, power, or privilege. A waiver of any specific breach shall not be deemed a waiver of any subsequent breach of the same or any other provision.</li>
                    </ul>
                </div>
            </section>

            <!-- Execution and General Provisions Section: Standard legal clauses for contract integrity -->
            <section class="mb-10">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2">
                    7. Execution and General Provisions
                </h2>
                <p class="text-gray-700 mb-4 text-justify">
                    This section details the formal execution of the Agreement and other standard, yet essential, general legal provisions that govern its interpretation, enforceability, and overall integrity.
                </p>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.1. Contract Execution Formalities and Representations</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong>A. Signature Requirements:</strong> This Agreement may be executed using either **wet signatures** (original physical signatures) or **legally approved electronic signatures** (e.g., via DocuSign, Adobe Sign, or similar secure, compliant platforms, fully adhering to applicable e-signature laws such as the U.S. ESIGN Act or local equivalents). Both methods shall possess the same legal force and effect and be equally binding upon the Parties.</li>
                        <li><strong>B. Effective Date:</strong> This Agreement shall become legally binding and fully effective upon the date of its execution by both Parties, as explicitly specified on the first page of this Agreement (the "Effective Date").</li>
                        <li><strong>C. Counterparts:</strong> This Agreement may be executed in any number of separate counterparts, each of which shall be deemed an original, but all of which together shall constitute one and the same legal instrument. For the avoidance of doubt, facsimile and PDF signatures shall be treated as original signatures for all purposes of this Agreement.</li>
                        <li><strong>D. Corporate Authority and Representations:</strong> Mewayz Global Limited hereby represents and warrants to the CBDO that it is a duly organized, validly existing, and in good standing under the laws of its jurisdiction of incorporation, has full corporate power and authority to enter into and execute this Agreement, and that the signatory for Mewayz has been duly authorized by its Board of Directors to bind the Company to all terms and conditions herein. The CBDO likewise represents and warrants their legal capacity, full authority, and absence of any prior conflicting agreements that would prevent their full performance hereunder.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.2. Governing Law, Exclusive Jurisdiction, and Regulatory Compliance</h3>
                    <ul class="list-disc list-inside space-y-2 text-gray-700 ml-4">
                        <li><strong class="text-blue-700">A. Governing Law:</strong> This Agreement, including its formation, validity, interpretation, and performance, shall be exclusively governed by, construed, and enforced in accordance with the substantive laws of <strong class="text-blue-700">Belgium</strong>, without regard to its conflict of laws principles that might otherwise apply the law of another jurisdiction. This explicit choice of law is fundamental to the Parties' agreement and a material term hereof.</li>
                        <li><strong>B. Exclusive Jurisdiction (Subject to Arbitration):</strong> Subject to the mandatory and exclusive arbitration provisions in Section 6.1, the Parties irrevocably agree that any legal action or proceeding arising out of or relating to this Agreement, including any action for interim relief permitted under Section 6.1.D, shall be brought exclusively in the courts located in <strong class="text-blue-600">Brussels, Belgium</strong>. Each Party hereby irrevocably and unconditionally submits to the exclusive personal jurisdiction of such courts for any such legal action or proceeding.</li>
                        <li><strong>C. Securities Compliance:</strong> Both Parties acknowledge and agree that this Agreement and the equity grant made hereunder are intended to be structured and executed in full compliance with all applicable federal, state, local, and international securities laws and regulations of the governing jurisdiction, as well as any other relevant jurisdictions where the CBDO resides, where Mewayz conducts business, or where securities may be deemed to be offered or issued.</li>
                        <li><strong>D. Employment Law vs. Partnership Status:</strong> While this is fundamentally a partnership agreement, defining the CBDO as an independent contractor providing executive services, Mewayz agrees to operate and engage with the CBDO in a manner that respects and complies with any applicable mandatory labor and employment regulations to the extent they may be deemed applicable to certain aspects of the relationship (e.g., workplace safety standards, anti-discrimination laws). The CBDO explicitly acknowledges and agrees they are acting as an independent contractor in this partnership capacity and are not an employee of Mewayz, unless explicitly re-designated by a separate, formal written employment agreement.</li>
                        <li><strong>E. International Compliance and Anti-Corruption:</strong> Both Parties commit to adhering to all relevant international business, trade, and partnership regulations, including but not limited to anti-bribery laws (e.g., U.S. Foreign Corrupt Practices Act (FCPA), UK Bribery Act), anti-money laundering (AML) laws, sanctions regulations, data privacy regulations (e.g., GDPR, CCPA), and intellectual property protection laws, as Mewayz expands its global operations and engages with international CBDOs.</li>
                    </ul>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.3. Severability and Judicial Reformation</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        If any provision of this Agreement is held by a court of competent jurisdiction or arbitrator to be invalid, illegal, or unenforceable in any respect, such invalidity, illegality, or unenforceability shall not affect any other provision of this Agreement, and the Agreement shall be construed as if such invalid, illegal, or unenforceable provision had never been contained herein. Furthermore, the Parties explicitly agree that in such an event, the court or arbitrator shall reform the offending provision to the extent necessary to render it enforceable, and to the maximum extent legally permissible, shall construe and enforce the Agreement as if such invalid, illegal, or unenforceable provision had been limited or eliminated in a manner that most closely approximates the original intent and economic effect of the invalid provision, thereby preserving the fundamental purpose of the Agreement.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.4. Entire Agreement and Supersedence</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        This Agreement, including all appendices, schedules, and exhibits specifically referenced and formally attached herein, constitutes the entire and exclusive agreement and understanding between the Parties with respect to the subject matter hereof. It expressly supersedes all prior discussions, negotiations, understandings, and agreements, whether written, oral, or implied, between the Parties relating to such subject matter. No modification, amendment, or waiver of any provision of this Agreement shall be effective unless made in writing and formally signed by both Parties as per Section 6.3. This clause ensures that the entire understanding is contained within this single, comprehensive document.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.5. Notices and Formal Communication Protocol</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        All notices, requests, consents, claims, demands, waivers, and other formal communications required or permitted hereunder shall be in writing and shall be deemed to have been duly given: (a) when delivered personally by hand (with written confirmation of receipt); (b) when received by the addressee if sent by a nationally or internationally recognized overnight courier (e.g., FedEx, DHL) with proof of delivery); (c) on the date sent by email (with confirmation of transmission and without an automated error message) if sent during normal business hours of the recipient, and on the next business day if sent after normal business hours of the recipient; or (d) upon actual receipt, whichever occurs first.
                    </p>
                    <p class="text-gray-700 mt-2 text-justify">Formal notices shall be sent to the Parties at the primary email addresses provided at the beginning of this Agreement. Either Party may designate a different physical address or email address for notices by providing formal written notice to the other Party at least five (5) business days' prior to such change becoming effective.</p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.6. Headings for Convenience and Interpretation</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        The headings and subheadings (e.g., "1. APPOINTMENT AND ROLE", "1.1 Position, Authority, and Reporting Structure") contained in this Agreement are included for convenience of reference and organization only. They shall not in any way affect the meaning or interpretation of this Agreement, nor shall they be deemed to limit or expand the scope of any provision hereof.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.7. Binding Effect, Successors, and Permitted Assigns</h3>
                    <p class="text-700 leading-relaxed text-justify">
                        This Agreement shall be legally binding upon and inure to the benefit of the Parties hereto and their respective heirs, executors, administrators, legal representatives, successors, and permitted assigns. The CBDO may not assign, delegate, or otherwise transfer any of their rights or obligations under this Agreement without the prior express written consent of Mewayz, which consent may be withheld in Mewayz's sole discretion. Mewayz may assign its rights and obligations under this Agreement, in whole or in part, to an affiliate or in connection with a merger, consolidation, acquisition, sale of all or substantially all of its assets, or other corporate reorganization, without the CBDO's consent.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.8. Independent Legal Counsel Acknowledgment</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        Each Party hereto acknowledges and represents that it has had the opportunity to consult with, and has been advised by, its own independent legal counsel concerning this Agreement and its terms, rights, obligations, and implications, or has knowingly waived such opportunity. Each Party further acknowledges that they fully understand and agree to the terms and conditions contained herein, and that this Agreement is the result of fair negotiations between the Parties.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.9. No Strict Construction</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        The language used in this Agreement shall be deemed to be the language chosen by the Parties to express their mutual intent, and no rule of strict construction shall be applied against any Party.
                    </p>
                </div>

                <div class="mb-6 bg-gray-50 p-6 rounded-md border border-gray-200">
                    <h3 class="font-semibold text-gray-900 mb-3">7.10. Business Days</h3>
                    <p class="text-gray-700 leading-relaxed text-justify">
                        Unless otherwise specified, "business days" refer to days on which commercial banks are open for business in both <strong class="text-blue-600">Brussels, Belgium</strong> and <strong class="text-blue-600">India</strong>.
                    </p>
                </div>
            </section>

            <!-- Signatures Section: Formal execution block -->
            <section id="signature-section" class="mb-10 text-gray-700">
                <h2 class="font-bold text-blue-800 mb-6 border-b-2 border-blue-700 pb-2 text-center uppercase">
                    Signatures and Acknowledgment of Acceptance
                </h2>
                <p class="text-gray-600 text-center mb-8 italic text-justify">
                    IN WITNESS WHEREOF, the Parties hereto have caused this Chief Business Development Officer Partnership Agreement to be executed by their duly authorized representatives as of the Effective Date first written above. This Agreement represents a comprehensive, legally robust, and meticulously detailed framework for the Chief Business Development Officer role at Mewayz Global Limited. Both parties are strongly advised and encouraged to seek independent legal counsel before its execution to fully understand all terms, implications, rights, and obligations contained herein.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                    <div class="flex flex-col items-start space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900 border-b border-gray-300 pb-2 w-full uppercase">For the Company:</h3>
                        <p class="text-lg font-medium text-blue-700">Mewayz Global Limited</p>
                        <div class="w-full">
                            <p class="mb-1 text-gray-700">Signature: <img id="companySignatureImage" class="signature-image" src="" alt="Company Signature"></p>
                            <p class="mb-1 text-gray-700">Name: Toon Monnens</p>
                            <p class="mb-1 text-gray-700">Title: <span class="text-blue-600">[Title, e.g., Chief Executive Officer, Founder]</span></p>
                            <p class="mb-1 text-gray-700">Date: _______________________</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-start space-y-4">
                        <h3 class="text-xl font-semibold text-gray-900 border-b border-gray-300 pb-2 w-full uppercase">For the Chief Business Development Officer:</h3>
                        <p class="text-lg font-medium text-blue-700">Shuvrajit Bhaduri</p>
                        <div class="w-full">
                            <div id="signatureInputSection">
                                <label for="signatureCanvas" class="text-gray-700 font-medium mb-2 block">Draw your signature below:</label>
                                <canvas id="signatureCanvas" width="300" height="100"></canvas>
                                <button id="clearSignatureBtn" class="mt-2 mr-2 px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition duration-300 ease-in-out">Clear Signature</button>
                                <button id="signDocumentBtn" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300 ease-in-out">Sign Document</button>
                            </div>
                            <p class="mb-1 text-gray-700 mt-4">Signature: <img id="partnerSignatureImage" class="signature-image" src="" alt="Chief Business Development Officer Signature"></p>
                            <p class="mb-1 text-gray-700">Name: Shuvrajit Bhaduri</p>
                            <p class="mb-1 text-gray-700">Title: Chief Business Development Officer</p>
                            <p class="mb-1 text-gray-700">Date: <span id="partnerSignatureDate">_______________________</span></p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-center mb-12">
                    <h3 class="text-xl font-semibold text-gray-900 border-b border-gray-300 pb-2 w-1/2 text-center uppercase">Witness Attestation:</h3>
                    <div class="mt-4 w-1/2">
                        <p class="mb-1 text-gray-700">Signature: _______________________</p>
                        <p class="mb-1 text-gray-700">Printed Name: _________________</p>
                        <p class="mb-1 text-gray-700">Date: _______________________</p>
                        <p class="text-gray-600 text-sm mt-2 italic text-center">A witness attests to the signing of this Agreement by both parties, confirming identities and witnessing execution.</p>
                    </div>
                </div>

                <div class="flex flex-col items-center">
                    <h3 class="text-xl font-semibold text-gray-900 border-b border-gray-300 pb-2 w-1/2 text-center uppercase">Notary Acknowledgment:</h3>
                    <div class="mt-4 w-full md:w-3/4 lg:w-1/2 text-gray-600 text-sm italic text-justify">
                        <p class="mb-1">[This section is optional but highly recommended for formal legal agreements, especially in certain jurisdictions, to provide additional legal verification of signatures. It involves a notary public acknowledging the signatures.]</p>
                        <p class="mb-1">State/Province of <span class="text-blue-600">[State/Province]</span>, County/District of <span class="text-blue-600">[County/District]</span></p>
                        <p class="mb-1">On this _____ day of __________, 20____, before me, a Notary Public in and for said County/District and State/Province, personally appeared <strong class="text-blue-700">Toon Monnens</strong>, known to me or proved to me on the basis of satisfactory evidence to be the <span class="text-blue-600">[Title]</span> of Mewayz Global Limited, and <strong class="text-blue-700">Shuvrajit Bhaduri</strong>, known to me or proved to me on the basis of satisfactory evidence to be the Chief Business Development Officer named in the foregoing instrument. Each of them acknowledged to me that they executed the same in their authorized capacities, and that by their respective signatures on the instrument, the entity or the person upon behalf of which the instrument was executed, executed the instrument.</p>
                        <p class="mb-1">IN WITNESS WHEREOF, I have hereunto set my hand and official seal.</p>
                        <p class="mt-4">_________________________</p>
                        <p>Notary Public Signature</p>
                        <p>Printed Name: _________________</p>
                        <p>My Commission Expires: _______</p>
                        <p>[Notary Seal/Stamp]</p>
                    </div>
                </div>

                <div class="text-center mt-12">
                    <button id="downloadPdfBtn" class="px-8 py-3 bg-blue-700 text-white font-semibold rounded-lg shadow-lg hover:bg-blue-800 transition duration-300 ease-in-out transform hover:scale-105">Download Signed Agreement as PDF</button>
                    <div id="loadingIndicator" class="mt-4 text-gray-600 hidden">
                        <p>Generating PDF, please wait...</p>
                    </div>
                </div>
            </section>

            <!-- Footer for clarity and legal notices -->
            <footer class="text-center text-gray-500 text-sm mt-12">
                <p>&copy; 2025 Mewayz Global Limited. All rights reserved. This document is strictly confidential and proprietary to Mewayz Global Limited. Unauthorized reproduction, distribution, or disclosure is strictly prohibited without explicit written consent.</p>
            </footer>
        </div>
    </div>

    <script>
        // JavaScript for Signature Pad and PDF Generation
        document.addEventListener('DOMContentLoaded', () => {
            const canvas = document.getElementById('signatureCanvas');
            const ctx = canvas.getContext('2d');
            const clearButton = document.getElementById('clearSignatureBtn');
            const signButton = document.getElementById('signDocumentBtn');
            const partnerSignatureImage = document.getElementById('partnerSignatureImage');
            const partnerSignatureDate = document.getElementById('partnerSignatureDate');
            const downloadPdfBtn = document.getElementById('downloadPdfBtn');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const agreementContent = document.getElementById('agreement-content');
            const signatureInputSection = document.getElementById('signatureInputSection');

            let drawing = false;
            let lastX = 0;
            let lastY = 0;

            // Initialize canvas for drawing
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.strokeStyle = '#000000'; // Black ink for signature

            // Event Listeners for drawing
            canvas.addEventListener('mousedown', (e) => {
                drawing = true;
                [lastX, lastY] = [e.offsetX, e.offsetY];
            });

            canvas.addEventListener('mousemove', (e) => {
                if (!drawing) return;
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(e.offsetX, e.offsetY);
                ctx.stroke();
                [lastX, lastY] = [e.offsetX, e.offsetY];
            });

            canvas.addEventListener('mouseup', () => {
                drawing = false;
            });

            canvas.addEventListener('mouseout', () => {
                drawing = false;
            });

            // For touch devices
            canvas.addEventListener('touchstart', (e) => {
                e.preventDefault(); // Prevent scrolling
                drawing = true;
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                [lastX, lastY] = [touch.clientX - rect.left, touch.clientY - rect.top];
            });

            canvas.addEventListener('touchmove', (e) => {
                e.preventDefault(); // Prevent scrolling
                if (!drawing) return;
                const touch = e.touches[0];
                const rect = canvas.getBoundingClientRect();
                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
                ctx.stroke();
                [lastX, lastY] = [touch.clientX - rect.left, touch.clientY - rect.top];
            });

            canvas.addEventListener('touchend', () => {
                drawing = false;
            });

            // Clear signature button
            clearButton.addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                partnerSignatureImage.src = ""; // Clear displayed image
                partnerSignatureDate.textContent = "_______________________"; // Clear date
            });

            // Sign document button
            signButton.addEventListener('click', () => {
                if (!canvas.toDataURL() || canvas.toDataURL() === canvas.toDataURL('image/png').replace("data:image/png;base64,", "").replace(/./g, 'A') || ctx.getImageData(0, 0, canvas.width, canvas.height).data.every(channel => channel === 0)) {
                    alert('Please draw your signature before signing.');
                    return;
                }
                partnerSignatureImage.src = canvas.toDataURL(); // Display signature on page
                partnerSignatureDate.textContent = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }); // Set current date
                alert('Signature captured! You can now download the PDF.');
            });

            // Download PDF button
            downloadPdfBtn.addEventListener('click', () => {
                loadingIndicator.classList.remove('hidden'); // Show loading indicator
                downloadPdfBtn.disabled = true; // Disable button during PDF generation

                // Temporarily hide signature input section for PDF generation
                signatureInputSection.classList.add('hide-on-pdf');

                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4'); // 'p' for portrait, 'mm' for millimeters, 'a4' for A4 size

                // Options for the html method
                const options = {
                    callback: function (pdf) {
                        pdf.save('Mewayz_Partnership_Agreement_Signed.pdf');
                        loadingIndicator.classList.add('hidden'); // Hide loading indicator
                        downloadPdfBtn.disabled = false; // Re-enable button
                        signatureInputSection.classList.remove('hide-on-pdf'); // Ensure it's visible again
                        alert('PDF downloaded successfully!');
                    },
                    margin: [25, 20, 25, 20], // Adjusted margins for a more professional look (top, right, bottom, left)
                    autoPaging: 'text', // Attempts to intelligently break pages based on text flow
                    html2canvas: {
                        scale: 0.8, // Adjust scale for better fit and resolution on A4 page
                        logging: false, // Disable html2canvas logging
                        // You can specify `letterRendering: true` for better text rendering if needed
                        // letterRendering: true,
                        useCORS: true // Essential for images from other origins if any
                    }
                };

                // Use the html method directly on the agreement content element
                pdf.html(agreementContent, options);

            });
        });
    </script>
</body>
</html>
