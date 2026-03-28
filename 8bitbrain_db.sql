-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 28, 2026 at 08:24 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `8bitbrain_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_text`, `is_correct`) VALUES
(2081, 521, 'A. Hard drive', 0),
(2082, 521, 'B. RAM', 0),
(2083, 521, 'C. CPU', 1),
(2084, 521, 'D. Monitor', 0),
(2085, 522, 'A. Microsoft Word', 0),
(2086, 522, 'B. Keyboard', 1),
(2087, 522, 'C. Google Chrome', 0),
(2088, 522, 'D. Windows', 0),
(2089, 523, 'A. Application software', 0),
(2090, 523, 'B. Operating system', 1),
(2091, 523, 'C. Utility software', 0),
(2092, 523, 'D. Web software', 0),
(2093, 524, 'A. System software', 0),
(2094, 524, 'B. Application software', 1),
(2095, 524, 'C. Network software', 0),
(2096, 524, 'D. Firmware', 0),
(2097, 525, 'A. Stores files permanently', 0),
(2098, 525, 'B. Temporarily stores data and programs in use', 1),
(2099, 525, 'C. Controls Internet connections', 0),
(2100, 525, 'D. Displays images', 0),
(2101, 526, 'A. Physical parts of a computer', 0),
(2102, 526, 'B. A set of instructions that tells a computer what to do', 1),
(2103, 526, 'C. Internet connection', 0),
(2104, 526, 'D. A storage device', 0),
(2105, 527, 'A. Monitor', 0),
(2106, 527, 'B. Mouse', 0),
(2107, 527, 'C. Microsoft Word', 1),
(2108, 527, 'D. Printer', 0),
(2109, 528, 'A. Application software', 0),
(2110, 528, 'B. System software', 1),
(2111, 528, 'C. Programming software', 0),
(2112, 528, 'D. Utility software', 0),
(2113, 529, 'A. Google Chrome', 0),
(2114, 529, 'B. Operating system', 1),
(2115, 529, 'C. Microsoft Excel', 0),
(2116, 529, 'D. Photoshop', 0),
(2117, 530, 'A. System software', 0),
(2118, 530, 'B. Application software', 1),
(2119, 530, 'C. Middleware', 0),
(2120, 530, 'D. Driver software', 0),
(2121, 531, 'A. Stores data', 0),
(2122, 531, 'B. Allows different software programs to communicate', 1),
(2123, 531, 'C. Controls hardware', 0),
(2124, 531, 'D. Runs applications', 0),
(2125, 532, 'A. Application software', 0),
(2126, 532, 'B. System software', 0),
(2127, 532, 'C. Programming software', 1),
(2128, 532, 'D. Utility software', 0),
(2129, 533, 'A. Windows', 0),
(2130, 533, 'B. BIOS', 0),
(2131, 533, 'C. Microsoft PowerPoint', 1),
(2132, 533, 'D. Device driver', 0),
(2133, 534, 'A. Creates documents', 0),
(2134, 534, 'B. Manages hardware and runs other software', 1),
(2135, 534, 'C. Connects to the Internet', 0),
(2136, 534, 'D. Stores data permanently', 0),
(2137, 535, 'A. It makes games work', 0),
(2138, 535, 'B. It allows the computer to function and run applications', 1),
(2139, 535, 'C. It connects the Internet', 0),
(2140, 535, 'D. It saves files', 0),
(2141, 536, 'A. Application software', 0),
(2142, 536, 'B. System software', 0),
(2143, 536, 'C. Programming software', 0),
(2144, 536, 'D. Keyboard', 1),
(2145, 537, 'A. System software', 0),
(2146, 537, 'B. Application software', 1),
(2147, 537, 'C. Middleware', 0),
(2148, 537, 'D. Firmware', 0),
(2149, 538, 'A. To control hardware', 0),
(2150, 538, 'B. To play music', 0),
(2151, 538, 'C. To help developers write and test programs', 1),
(2152, 538, 'D. To store data', 0),
(2153, 539, 'A. Word processor', 0),
(2154, 539, 'B. Operating system', 1),
(2155, 539, 'C. Web browser', 0),
(2156, 539, 'D. Game software', 0),
(2157, 540, 'A. To supply electricity', 0),
(2158, 540, 'B. To cool the system', 0),
(2159, 540, 'C. To tell the hardware how to work', 1),
(2160, 540, 'D. To store files', 0),
(2161, 541, 'A. A single supercomputer', 0),
(2162, 541, 'B. A global system of interconnected computer networks', 1),
(2163, 541, 'C. A social media platform', 0),
(2164, 541, 'D. A type of software', 0),
(2165, 542, 'A. Centralized', 0),
(2166, 542, 'B. Isolated', 0),
(2167, 542, 'C. Decentralized', 1),
(2168, 542, 'D. Manual', 0),
(2169, 543, 'A. Browsers', 0),
(2170, 543, 'B. Cables', 0),
(2171, 543, 'C. Communication protocols', 1),
(2172, 543, 'D. Web pages', 0),
(2173, 544, 'A. HTML', 0),
(2174, 544, 'B. FTP', 0),
(2175, 544, 'C. TCP/IP', 1),
(2176, 544, 'D. USB', 0),
(2177, 545, 'A. Ethernet', 0),
(2178, 545, 'B. Bluetooth', 0),
(2179, 545, 'C. ARPANET', 1),
(2180, 545, 'D. Intranet', 0),
(2181, 546, 'A. Storing data in one place', 0),
(2182, 546, 'B. Sending data in small pieces across different paths', 1),
(2183, 546, 'C. Encrypting data', 0),
(2184, 546, 'D. Blocking data', 0),
(2185, 547, 'A. A user\'s laptop', 0),
(2186, 547, 'B. A computer that provides data or services to other computers', 1),
(2187, 547, 'C. A cable', 0),
(2188, 547, 'D. A router', 0),
(2189, 548, 'A. Store websites', 0),
(2190, 548, 'B. Request information or services from servers', 1),
(2191, 548, 'C. Route data', 0),
(2192, 548, 'D. Manage cables', 0),
(2193, 549, 'A. Copper wires', 0),
(2194, 549, 'B. Radio waves only', 0),
(2195, 549, 'C. Fiber-optic cables', 1),
(2196, 549, 'D. Paper', 0),
(2197, 550, 'A. The entire Internet', 0),
(2198, 550, 'B. A system of interlinked web pages accessed over the Internet', 1),
(2199, 550, 'C. An email service', 0),
(2200, 550, 'D. A computer network', 0),
(2201, 551, 'A. Communication', 0),
(2202, 551, 'B. Commerce', 0),
(2203, 551, 'C. Education', 0),
(2204, 551, 'D. Electricity generation', 1),
(2205, 552, 'A. One controlling company', 0),
(2206, 552, 'B. Open standards and protocols', 1),
(2207, 552, 'C. Local cables only', 0),
(2208, 552, 'D. Offline software', 0),
(2209, 553, 'A. Create web pages', 0),
(2210, 553, 'B. Provide users with access to the Internet', 1),
(2211, 553, 'C. Store all Internet data', 0),
(2212, 553, 'D. Control all websites', 0),
(2213, 554, 'A. Modems', 0),
(2214, 554, 'B. Routers', 0),
(2215, 554, 'C. The World Wide Web', 1),
(2216, 554, 'D. Firewalls', 0),
(2217, 555, 'A. It replaces computers', 0),
(2218, 555, 'B. It stores electricity', 0),
(2219, 555, 'C. It enables global communication, information sharing, and online services', 1),
(2220, 555, 'D. It only supports entertainment', 0),
(2221, 556, 'A. Running programs on a computer', 0),
(2222, 556, 'B. Connecting computers and devices to share data and resources', 1),
(2223, 556, 'C. Building computer hardware', 0),
(2224, 556, 'D. Creating websites', 0),
(2225, 557, 'A. To make computers faster', 0),
(2226, 557, 'B. To allow devices to communicate and share resources', 1),
(2227, 557, 'C. To store electricity', 0),
(2228, 557, 'D. To play games', 0),
(2229, 558, 'A. Calculator', 0),
(2230, 558, 'B. Smartphone', 1),
(2231, 558, 'C. Flash drive', 0),
(2232, 558, 'D. Battery', 0),
(2233, 559, 'A. Stores files', 0),
(2234, 559, 'B. Sends data between different networks', 1),
(2235, 559, 'C. Displays images', 0),
(2236, 559, 'D. Runs applications', 0),
(2237, 560, 'A. Long Area Network', 0),
(2238, 560, 'B. Local Area Network', 1),
(2239, 560, 'C. Large Access Network', 0),
(2240, 560, 'D. Linked Area Network', 0),
(2241, 561, 'A. LAN', 0),
(2242, 561, 'B. WAN', 1),
(2243, 561, 'C. PAN', 0),
(2244, 561, 'D. VPN', 0),
(2245, 562, 'A. Displays web pages', 0),
(2246, 562, 'B. Protects against viruses', 0),
(2247, 562, 'C. Provides rules for how data is sent and received', 1),
(2248, 562, 'D. Stores data', 0),
(2249, 563, 'A. A personal computer', 0),
(2250, 563, 'B. A computer that provides services or data to other computers', 1),
(2251, 563, 'C. A cable', 0),
(2252, 563, 'D. A keyboard', 0),
(2253, 564, 'A. A router', 0),
(2254, 564, 'B. A server', 0),
(2255, 564, 'C. A device that requests services or data', 1),
(2256, 564, 'D. A modem', 0),
(2257, 565, 'A. The size of a computer', 0),
(2258, 565, 'B. The number of devices', 0),
(2259, 565, 'C. The amount of data that can be transmitted', 1),
(2260, 565, 'D. The type of network', 0),
(2261, 566, 'A. Higher electricity use', 0),
(2262, 566, 'B. Sharing files and printers', 1),
(2263, 566, 'C. Slower computers', 0),
(2264, 566, 'D. Less security', 0),
(2265, 567, 'A. Router', 0),
(2266, 567, 'B. Switch', 1),
(2267, 567, 'C. Monitor', 0),
(2268, 567, 'D. Scanner', 0),
(2269, 568, 'A. Wired network', 0),
(2270, 568, 'B. Wireless network', 1),
(2271, 568, 'C. Fiber network', 0),
(2272, 568, 'D. LAN', 0),
(2273, 569, 'A. To play music', 0),
(2274, 569, 'B. To share information and communicate efficiently', 1),
(2275, 569, 'C. To save power', 0),
(2276, 569, 'D. To install games', 0),
(2277, 570, 'A. It replaces computers', 0),
(2278, 570, 'B. It removes software', 0),
(2279, 570, 'C. It enables communication, data sharing, and online services', 1),
(2280, 570, 'D. It stops Internet access', 0),
(2281, 571, 'A. Creating websites', 0),
(2282, 571, 'B. Protecting systems, networks, and data from attacks', 1),
(2283, 571, 'C. Building computers', 0),
(2284, 571, 'D. Selling software', 0),
(2285, 572, 'A. A slow computer', 0),
(2286, 572, 'B. A software update', 0),
(2287, 572, 'C. Any action that can harm a digital system or data', 1),
(2288, 572, 'D. A strong password', 0),
(2289, 573, 'A. Useful software', 0),
(2290, 573, 'B. Malicious software designed to damage or steal data', 1),
(2291, 573, 'C. A firewall', 0),
(2292, 573, 'D. A password', 0),
(2293, 574, 'A. Web browser', 0),
(2294, 574, 'B. Ransomware', 1),
(2295, 574, 'C. Email', 0),
(2296, 574, 'D. Spreadsheet', 0),
(2297, 575, 'A. Protects files', 0),
(2298, 575, 'B. Locks files and demands payment', 1),
(2299, 575, 'C. Backs up data', 0),
(2300, 575, 'D. Cleans viruses', 0),
(2301, 576, 'A. Fixing computers', 0),
(2302, 576, 'B. Tricking users into giving personal info', 1),
(2303, 576, 'C. Encrypting files', 0),
(2304, 576, 'D. Updating software', 0),
(2305, 577, 'A. Make computers faster', 0),
(2306, 577, 'B. Prevent unauthorized access', 1),
(2307, 577, 'C. Save electricity', 0),
(2308, 577, 'D. Delete viruses', 0),
(2309, 578, 'A. Deletes data', 0),
(2310, 578, 'B. Converts data into coded form', 1),
(2311, 578, 'C. Sends emails', 0),
(2312, 578, 'D. Turns off computers', 0),
(2313, 579, 'A. Ignoring updates', 0),
(2314, 579, 'B. Installing security patches', 1),
(2315, 579, 'C. Using weak passwords', 0),
(2316, 579, 'D. Sharing passwords', 0),
(2317, 580, 'A. Downloading software', 0),
(2318, 580, 'B. Sending emails', 0),
(2319, 580, 'C. Verifying user identity', 1),
(2320, 580, 'D. Cleaning viruses', 0),
(2321, 581, 'A. One password', 0),
(2322, 581, 'B. Two or more ways to prove identity', 1),
(2323, 581, 'C. Removing security', 0),
(2324, 581, 'D. Encrypting files', 0),
(2325, 582, 'A. A virus', 0),
(2326, 582, 'B. A password', 0),
(2327, 582, 'C. Controls network traffic for security', 1),
(2328, 582, 'D. A hacker', 0),
(2329, 583, 'A. Waste time', 0),
(2330, 583, 'B. May lead to phishing or malware', 1),
(2331, 583, 'C. Slow internet', 0),
(2332, 583, 'D. Delete', 0),
(2333, 584, 'A. System update', 0),
(2334, 584, 'B. Sensitive data accessed without permission', 1),
(2335, 584, 'C. Power failure', 0),
(2336, 584, 'D. Network upgrade', 0),
(2337, 585, 'A. Stop the internet', 0),
(2338, 585, 'B. Increase file size', 0),
(2339, 585, 'C. Protect digital information and systems', 1),
(2340, 585, 'D. Remove software', 0),
(2341, 587, '100', 1),
(2342, 587, '75', 0),
(2343, 587, '125', 0),
(2344, 588, '12', 1),
(2345, 588, '14', 0),
(2346, 588, '10', 0),
(2347, 589, '8', 1),
(2348, 589, '6', 0),
(2349, 589, '7', 0),
(2350, 590, 'Leonardo da Vinci', 1),
(2351, 590, 'Michelangelo', 0),
(2352, 590, 'Raphael', 0),
(2353, 591, 'Sahara', 1),
(2354, 591, 'Gobi', 0),
(2355, 591, 'Kalahari', 0),
(2356, 592, 'Ottawa', 1),
(2357, 592, 'Toronto', 0),
(2358, 592, 'Vancouver', 0),
(2359, 593, 'Oxygen', 1),
(2360, 593, 'Carbon Dioxide', 0),
(2361, 593, 'Nitrogen', 0),
(2362, 594, 'Nucleus', 1),
(2363, 594, 'Electron', 0),
(2364, 594, 'Proton', 0),
(2365, 595, 'Venus', 1),
(2366, 595, 'Mars', 0),
(2367, 595, 'Mercury', 0),
(2368, 596, 'Movement of water across a membrane', 1),
(2369, 597, 'Step-by-step procedure for solving a problem', 1),
(2370, 598, 'Community of organisms interacting with environment', 1),
(2371, 599, 'Na', 1),
(2372, 599, 'So', 0),
(2373, 599, 'Sn', 0),
(2374, 600, 'Mitochondria', 1),
(2375, 600, 'Nucleus', 0),
(2376, 600, 'Ribosome', 0),
(2377, 601, 'Carbon Dioxide', 1),
(2378, 601, 'Oxygen', 0),
(2379, 601, 'Nitrogen', 0),
(2380, 602, 'Saturn', 1),
(2381, 602, 'Jupiter', 0),
(2382, 602, 'Mars', 0),
(2383, 603, 'Diamond', 1),
(2384, 603, 'Quartz', 0),
(2385, 603, 'Steel', 0),
(2386, 604, '299,792 km/s', 1),
(2387, 604, '150,000 km/s', 0),
(2388, 604, '1,000 km/s', 0),
(2389, 605, 'Mars', 1),
(2390, 605, 'Venus', 0),
(2391, 605, 'Mercury', 0),
(2392, 606, 'Skin', 1),
(2393, 606, 'Liver', 0),
(2394, 606, 'Heart', 0),
(2395, 607, 'Nitrogen', 1),
(2396, 607, 'Oxygen', 0),
(2397, 607, 'Carbon Dioxide', 0),
(2398, 608, '100°C', 1),
(2399, 608, '90°C', 0),
(2400, 608, '110°C', 0),
(2401, 609, 'White blood cells', 1),
(2402, 609, 'Red blood cells', 0),
(2403, 609, 'Platelets', 0),
(2404, 610, 'The Sun', 1),
(2405, 610, 'Earth', 0),
(2406, 610, 'Jupiter', 0),
(2407, 611, 'Albert Einstein', 1),
(2408, 611, 'Isaac Newton', 0),
(2409, 611, 'Galileo Galilei', 0),
(2410, 612, 'Au', 1),
(2411, 612, 'Ag', 0),
(2412, 612, 'Gd', 0),
(2413, 613, 'Mantle', 1),
(2414, 613, 'Core', 0),
(2415, 613, 'Lithosphere', 0),
(2416, 614, 'Helium', 1),
(2417, 614, 'Hydrogen', 0),
(2418, 614, 'Oxygen', 0),
(2419, 615, 'Vitamin D', 1),
(2420, 615, 'Vitamin C', 0),
(2421, 615, 'Vitamin B12', 0),
(2422, 616, 'Cell', 1),
(2423, 616, 'Atom', 0),
(2424, 616, 'Molecule', 0),
(2425, 617, 'Jupiter', 1),
(2426, 617, 'Saturn', 0),
(2427, 617, 'Neptune', 0),
(2428, 618, 'Water', 1),
(2429, 618, 'Hydrogen', 0),
(2430, 618, 'Oxygen', 0),
(2431, 619, 'Isaac Newton', 1),
(2432, 619, 'Albert Einstein', 0),
(2433, 619, 'Galileo Galilei', 0),
(2434, 620, 'Fe', 1),
(2435, 620, 'Ir', 0),
(2436, 620, 'In', 0),
(2437, 621, 'Mercury', 1),
(2438, 621, 'Venus', 0),
(2439, 621, 'Earth', 0),
(2440, 622, 'Blue Whale', 1),
(2441, 622, 'Elephant', 0),
(2442, 622, 'Giraffe', 0),
(2443, 623, 'Na', 1),
(2444, 623, 'So', 0),
(2445, 623, 'Sn', 0),
(2446, 624, 'Mitochondria', 1),
(2447, 624, 'Nucleus', 0),
(2448, 624, 'Ribosome', 0),
(2449, 625, 'Carbon Dioxide', 1),
(2450, 625, 'Oxygen', 0),
(2451, 625, 'Nitrogen', 0),
(2452, 626, 'Saturn', 1),
(2453, 626, 'Jupiter', 0),
(2454, 626, 'Mars', 0),
(2455, 627, 'Diamond', 1),
(2456, 627, 'Quartz', 0),
(2457, 627, 'Steel', 0),
(2458, 628, '299,792 km/s', 1),
(2459, 628, '150,000 km/s', 0),
(2460, 628, '1,000 km/s', 0),
(2461, 629, 'Mars', 1),
(2462, 629, 'Venus', 0),
(2463, 629, 'Mercury', 0),
(2464, 630, 'Skin', 1),
(2465, 630, 'Liver', 0),
(2466, 630, 'Heart', 0),
(2467, 631, 'Nitrogen', 1),
(2468, 631, 'Oxygen', 0),
(2469, 631, 'Carbon Dioxide', 0),
(2470, 632, '100°C', 1),
(2471, 632, '90°C', 0),
(2472, 632, '110°C', 0),
(2473, 633, 'White blood cells', 1),
(2474, 633, 'Red blood cells', 0),
(2475, 633, 'Platelets', 0),
(2476, 634, 'The Sun', 1),
(2477, 634, 'Earth', 0),
(2478, 634, 'Jupiter', 0),
(2479, 635, 'Albert Einstein', 1),
(2480, 635, 'Isaac Newton', 0),
(2481, 635, 'Galileo Galilei', 0),
(2482, 636, 'Au', 1),
(2483, 636, 'Ag', 0),
(2484, 636, 'Gd', 0),
(2485, 637, 'Mantle', 1),
(2486, 637, 'Core', 0),
(2487, 637, 'Lithosphere', 0),
(2488, 638, 'Helium', 1),
(2489, 638, 'Hydrogen', 0),
(2490, 638, 'Oxygen', 0),
(2491, 639, 'Vitamin D', 1),
(2492, 639, 'Vitamin C', 0),
(2493, 639, 'Vitamin B12', 0),
(2494, 640, 'Cell', 1),
(2495, 640, 'Atom', 0),
(2496, 640, 'Molecule', 0),
(2497, 641, 'Jupiter', 1),
(2498, 641, 'Saturn', 0),
(2499, 641, 'Neptune', 0),
(2500, 642, 'Water', 1),
(2501, 642, 'Hydrogen', 0),
(2502, 642, 'Oxygen', 0),
(2503, 643, 'Isaac Newton', 1),
(2504, 643, 'Albert Einstein', 0),
(2505, 643, 'Galileo Galilei', 0),
(2506, 644, 'Fe', 1),
(2507, 644, 'Ir', 0),
(2508, 644, 'In', 0),
(2509, 645, 'Mercury', 1),
(2510, 645, 'Venus', 0),
(2511, 645, 'Earth', 0),
(2512, 646, 'Blue Whale', 1),
(2513, 646, 'Elephant', 0),
(2514, 646, 'Giraffe', 0);

-- --------------------------------------------------------

--
-- Table structure for table `leaderboard`
--

CREATE TABLE `leaderboard` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `mode` varchar(50) NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `correct` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`) VALUES
(521, 41, 'Which computer component is responsible for executing instructions?'),
(522, 41, 'Which of the following is an example of computer hardware?'),
(523, 41, 'What type of software manages the hardware and allows other software to run?'),
(524, 41, 'Which software is used to create documents, spreadsheets, or presentations?'),
(525, 41, 'What does RAM do in a computer?'),
(526, 42, 'What is software?'),
(527, 42, 'Which of the following is software?'),
(528, 42, 'What type of software controls and manages computer hardware?'),
(529, 42, 'Which is an example of system software?'),
(530, 42, 'What type of software helps users perform tasks such as typing, browsing, or editing photos?'),
(531, 42, 'What does middleware do?'),
(532, 42, 'What kind of software is used to create other software?'),
(533, 42, 'Which of the following is an example of application software?'),
(534, 42, 'What does an operating system do?'),
(535, 42, 'Why is system software important?'),
(536, 42, 'Which of the following is NOT a type of software?'),
(537, 42, 'What type of software helps users browse the Internet or write documents?'),
(538, 42, 'What is the role of programming software?'),
(539, 42, 'Which software runs first when you turn on a computer?'),
(540, 42, 'Why is software necessary in a computer?'),
(541, 43, 'What is the Internet?'),
(542, 43, 'Which term best describes how the Internet is organized?'),
(543, 43, 'What allows different networks on the Internet to communicate with each other?'),
(544, 43, 'Which protocol is fundamental to Internet communication?'),
(545, 43, 'Which early network is considered the foundation of today\'s Internet?'),
(546, 43, 'What is packet switching?'),
(547, 43, 'What is a server on the Internet?'),
(548, 43, 'What do clients do on the Internet?'),
(549, 43, 'Which physical medium carries much of today\'s Internet traffic?'),
(550, 43, 'What is the World Wide Web?'),
(551, 43, 'Which of the following is NOT a use of the Internet?'),
(552, 43, 'What makes the Internet able to grow and connect many networks?'),
(553, 43, 'What role do Internet Service Providers (ISPs) play?'),
(554, 43, 'Which invention made the Internet more user-friendly for the public?'),
(555, 43, 'Why is the Internet important to modern society?'),
(556, 44, 'What is computer networking?'),
(557, 44, 'What is the main purpose of a network?'),
(558, 44, 'Which of the following is an example of a networked device?'),
(559, 44, 'What does a router do in a network?'),
(560, 44, 'What is a LAN?'),
(561, 44, 'What type of network connects devices across large geographic areas?'),
(562, 44, 'What does TCP/IP do in networking?'),
(563, 44, 'What is a server in a network?'),
(564, 44, 'What is a client in a network?'),
(565, 44, 'What is bandwidth?'),
(566, 44, 'Which of these is a benefit of networking?'),
(567, 44, 'What device connects computers inside the same network?'),
(568, 44, 'What type of connection uses radio signals instead of cables?'),
(569, 44, 'Why are networks important in businesses?'),
(570, 44, 'What is one key role of networking in modern life?'),
(571, 45, 'What is cybersecurity mainly concerned with?'),
(572, 45, 'Which best describes a cyber threat?'),
(573, 45, 'What is malware?'),
(574, 45, 'Which is malware?'),
(575, 45, 'What does ransomware do?'),
(576, 45, 'What is phishing?'),
(577, 45, 'Why are strong passwords important?'),
(578, 45, 'What does encryption do?'),
(579, 45, 'Which helps keep system secure?'),
(580, 45, 'What is authentication?'),
(581, 45, 'What is MFA?'),
(582, 45, 'What is a firewall?'),
(583, 45, 'Why be careful with email links?'),
(584, 45, 'What is a data breach?'),
(585, 45, 'Why is cybersecurity important?'),
(586, 46, 'What is 25 x 4?'),
(587, 46, 'What is 25 x 4?'),
(588, 46, 'Solve: 144 ÷ 12'),
(589, 46, 'Square root of 64?'),
(590, 50, 'Who painted the Mona Lisa?'),
(591, 50, 'Which is the largest desert?'),
(592, 50, 'What is the capital of Canada?'),
(593, 54, 'What gas do plants release during photosynthesis?'),
(594, 54, 'What is the center of an atom called?'),
(595, 54, 'What planet is known as the Morning Star?'),
(596, 52, 'Define: Osmosis'),
(597, 52, 'Define: Algorithm'),
(598, 52, 'Define: Ecosystem'),
(599, 54, 'What is the chemical symbol for sodium?'),
(600, 54, 'Which organelle is known as the powerhouse of the cell?'),
(601, 54, 'What gas do humans exhale in large amounts?'),
(602, 54, 'What planet has the most moons?'),
(603, 54, 'What is the hardest natural substance on Earth?'),
(604, 54, 'What is the speed of light in vacuum?'),
(605, 54, 'Which planet is known as the Red Planet?'),
(606, 54, 'What is the largest organ in the human body?'),
(607, 54, 'Which gas is most abundant in Earth’s atmosphere?'),
(608, 54, 'What is the boiling point of water at sea level?'),
(609, 54, 'Which blood cells fight infection?'),
(610, 54, 'What is the center of our solar system?'),
(611, 54, 'Which scientist proposed the theory of relativity?'),
(612, 54, 'What is the chemical symbol for gold?'),
(613, 54, 'Which layer of Earth lies beneath the crust?'),
(614, 54, 'What is the main gas in balloons?'),
(615, 54, 'Which vitamin is produced when skin is exposed to sunlight?'),
(616, 54, 'What is the smallest unit of life?'),
(617, 54, 'Which planet is the largest in our solar system?'),
(618, 54, 'What is H2O commonly known as?'),
(619, 54, 'Which scientist discovered gravity?'),
(620, 54, 'What is the chemical symbol for iron?'),
(621, 54, 'Which planet is closest to the Sun?'),
(622, 54, 'What is the largest mammal on Earth?'),
(623, 54, 'What is the chemical symbol for sodium?'),
(624, 54, 'Which organelle is known as the powerhouse of the cell?'),
(625, 54, 'What gas do humans exhale in large amounts?'),
(626, 54, 'What planet has the most moons?'),
(627, 54, 'What is the hardest natural substance on Earth?'),
(628, 54, 'What is the speed of light in vacuum?'),
(629, 54, 'Which planet is known as the Red Planet?'),
(630, 54, 'What is the largest organ in the human body?'),
(631, 54, 'Which gas is most abundant in Earth’s atmosphere?'),
(632, 54, 'What is the boiling point of water at sea level?'),
(633, 54, 'Which blood cells fight infection?'),
(634, 54, 'What is the center of our solar system?'),
(635, 54, 'Which scientist proposed the theory of relativity?'),
(636, 54, 'What is the chemical symbol for gold?'),
(637, 54, 'Which layer of Earth lies beneath the crust?'),
(638, 54, 'What is the main gas in balloons?'),
(639, 54, 'Which vitamin is produced when skin is exposed to sunlight?'),
(640, 54, 'What is the smallest unit of life?'),
(641, 54, 'Which planet is the largest in our solar system?'),
(642, 54, 'What is H2O commonly known as?'),
(643, 54, 'Which scientist discovered gravity?'),
(644, 54, 'What is the chemical symbol for iron?'),
(645, 54, 'Which planet is closest to the Sun?'),
(646, 54, 'What is the largest mammal on Earth?');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `difficulty` enum('easy','medium','hard') DEFAULT 'medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mode` enum('single_player','timed_quiz','ranked_quiz','memory_match','endless_quiz') NOT NULL DEFAULT 'single_player'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `category`, `difficulty`, `created_at`, `mode`) VALUES
(41, 'Computer Hardware Fundamentals', 'Computer Hardware', 'easy', '2026-03-28 04:24:07', 'single_player'),
(42, 'Computer Software Essentials', 'Computer Software', 'easy', '2026-03-28 04:24:07', 'single_player'),
(43, 'Internet Basics', 'Internet', 'medium', '2026-03-28 04:24:07', 'single_player'),
(44, 'Computer Networking', 'Network', 'medium', '2026-03-28 04:24:07', 'single_player'),
(45, 'CyberSecurity Fundamentals', 'CyberSecurity', 'hard', '2026-03-28 04:24:07', 'single_player'),
(46, 'Math Speed Test', 'Math', 'medium', '2026-03-28 05:43:20', 'timed_quiz'),
(48, 'Math Speed Test', 'Math', 'medium', '2026-03-28 05:49:01', 'timed_quiz'),
(49, 'Quick History Facts', 'History', 'easy', '2026-03-28 05:49:01', 'timed_quiz'),
(50, 'General Knowledge Ranked', 'Trivia', 'hard', '2026-03-28 05:49:01', 'ranked_quiz'),
(52, 'Vocabulary Memory Match', 'English', 'easy', '2026-03-28 05:49:01', 'memory_match'),
(54, 'Endless Science Challenge', 'Science', 'medium', '2026-03-28 05:49:01', 'endless_quiz');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `account_type` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `username`, `age`, `password`, `account_type`, `created_at`) VALUES
(1, 'Admin', 'admin@8bitbrain.com', 'admin', 25, 'admin123', 'admin', '2026-03-13 04:51:10'),
(2, 'Hanz Galgana', 'hanzy1galgana@gmail.com', 'Hhanzi', 19, 'Hanzy1312', 'user', '2026-03-28 04:38:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2515;

--
-- AUTO_INCREMENT for table `leaderboard`
--
ALTER TABLE `leaderboard`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=647;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaderboard`
--
ALTER TABLE `leaderboard`
  ADD CONSTRAINT `leaderboard_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
