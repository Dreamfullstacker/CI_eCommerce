<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo $page_title . ' - ' .$product_name; ?></title>
    <meta name="description" content="">
    <meta name="author" content="ticlekiwi">

    <meta http-equiv="cleartype" content="on">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/favicon.png'); ?>">

    <link rel="stylesheet" href="<?php echo base_url('plugins/api/css/hightlightjs-dark.css'); ?>">
    <script src="<?php echo base_url('plugins/api/js/jquery.min.js'); ?>" crossorigin="anonymous"></script>
    <script src="<?php echo base_url('plugins/api/js/highlight.min.js'); ?>"></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,500|Source+Code+Pro:300" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url('plugins/api/css/style.css'); ?>" media="all">
    <script>hljs.initHighlightingOnLoad();</script>
    <style>
        .content-menu ul li a {
            font-size: .8rem;
        }
    </style>
</head>

<body>
<div class="left-menu">
    <div class="content-logo">
        <img alt="<?php echo $page_title . ' - ' .$product_name; ?>" title="<?php echo $page_title . ' - ' .$product_name; ?>" src="<?php echo base_url('assets/img/logo.png'); ?>" height="32" />
        <span><?php echo $this->lang->line('API Documentation</s'); ?>pan>
    </div>
    <div class="content-menu">
        <ul>
            <li class="scroll-to-link active" data-target="get-started">
                <a><?php echo $this->lang->line('GET STARTED'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="get-user">
                <a><?php echo $this->lang->line('Get User'); ?><sup>(admin)</sup></a>
            </li>
            </li>
            <li class="scroll-to-link" data-target="create-user">
                <a><?php echo $this->lang->line('Create User'); ?><sup>(admin)</sup></a>
            </li>
            <li class="scroll-to-link" data-target="update-user">
                <a><?php echo $this->lang->line('Update User'); ?><sup>(admin)</sup></a>
            <li class="scroll-to-link" data-target="get-all-packages">
                <a><?php echo $this->lang->line('Get Packages'); ?><sup>(admin)</sup></a>
            </li>
            <li class="scroll-to-link" data-target="get-subscriber">
                <a><?php echo $this->lang->line('Get Subscriber'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="get-all-labels">
                <a><?php echo $this->lang->line('Get Labels'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="create-label">
                <a><?php echo $this->lang->line('Create Label'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="assign-label">
                <a><?php echo $this->lang->line('Assign Label'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="get-contact-group">
                <a><?php echo $this->lang->line('Get Contact Group'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="create-contact-group">
                <a><?php echo $this->lang->line('Create Contact Group'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="update-contact-group">
                <a><?php echo $this->lang->line('Update Contact Group'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="delete-contact-group">
                <a><?php echo $this->lang->line('Delete Contact Group'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="get-contact">
                <a><?php echo $this->lang->line('Get Contact'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="create-contact">
                <a><?php echo $this->lang->line('Create Contact'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="update-contact">
                <a><?php echo $this->lang->line('Update Contact'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="delete-contact">
                <a><?php echo $this->lang->line('Delete Contact'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="flow-campaign-list">
                <a><?php echo $this->lang->line('Flow Campaigns'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="flow-campaign-info">
                <a><?php echo $this->lang->line('Flow Campaign Info'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="single-subscriber-flow-info">
                <a><?php echo $this->lang->line('Single Subscriber Flow Info'); ?></a>
            </li>
            <li class="scroll-to-link" data-target="all-subscribers-flow-info">
                <a><?php echo $this->lang->line('All Subscribers Flow Info'); ?></a>
            </li>
        </ul>
    </div>
</div>
<div class="content-page">
    <div class="content-code"></div>
    <div class="content">
        <div class="overflow-hidden content-section" id="content-get-started">
            <h1 id="get-started">Get started</h1>
            <pre>
    <?php echo $this->lang->line('API Endpoint'); ?>
        <br>
        <?php echo $endpoint; ?>
                </pre>
            <p>
                The <?php echo $product_name; ?> API is organized around REST. Our API has predictable resource-oriented URLs, accepts form-encoded request bodies, returns JSON-encoded responses, common HTTP verbs and uses API key based authentication.
            </p>
            <p>
                To use this API, you need an <strong>API key</strong>. To get your own API key, please click on the profile picture on the top-right-side on your dashboard and then click on the API Key menu.
            </p>

            <p>
                <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABc4AAANaCAYAAACwV99OAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAAJOgAACToAYJjBRwAAPUdSURBVHhe7L2Jn1Plvfj//Bm/+/3eLrbXFrsNtZUuFlwRlGERZJVl2IdFdkRRQcFBQFTEUVAsttyLLZaqiLbUVikuSFu4tJXS23L91q21Yl2GRYbFfH6fJ+ckOefkSSaZSTInmff79Xo7M8nJyUkYSJ53Hp9junXrJoiIiIiIiIiIiIiI6Ek4R0REREREREREREQMaKYu+lAQEREREREREREREdGTcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIiIiIGJBwjoiIiIiIiIiIiIgYkHCOiIiIiIiIiIiIiBiQcI6IiIiIiIiIWFWamOg6NkTE2pBwjoiIiIiIiIjYabqCdDXreoyIiNUn4RwRERERERERseK6onOt6XrciIjVIeEcEREREREREbHiukJzrep6/IiI8ZZwjoiIiIiIiIhYUV1xuSvqem4QEeMh4RwRERERERERsaK6InJX1fX8ICJ2voRzRERERERERMSK6grIXVnXc4SI2LkSzhERERERERERK6orHqP7uUJE7BwJ54iIiIiIiIiIFdUVjUvvzMVGFiwxctMyI0uXG7llqZEl+vNS9Xa97Ha9bNltRm6z6s+32m306w16/byb9fY3uvdbPl3PFSJi50g4R0RERERERMR2+gG2S1c07rjTbjCy4BYjS5YbWbXKyF1rjKy50wvkUycbGTfayNhr9ftJRm683sgtNxm5damRZbd6Ib3pdiMrfO331qV6/cIbjczSfTc67rO0up4r7Nq6/t1BrIyEc0RERERERETMoStkYcd1ReP2O3+JkduajKxZbWT1SiN33Wlk7T2ed+plc2YZuWawkQnjjPS/ysiVVxiZNMHI3DlGbtDb33yjkSU2ot9iZKl621Ivtjep9vubbtDrbzayWL9Oma7q7VzH0XFdzxViPl3/biGWRsI5IiIiIiIiYpfTFaCwcrqicfHecKuR1XcauecuTxvM71xl5O41Ru5d63295UYjo0cauexiI5deZOTCbxv51jeNXHGZkcEDjTRONnLddN3fJCPjRxsZN0Z/nmlksd7OLuNil3WxYX3pzXp/C41M0e2m6G1m6DZTZmUfU8d0PVeIpdD17yBifgnniIiIiIiIiDWnKxxhfHRF48JdZIP5ai+Wr73byN02nOtXG87trHP7vZ0xPn6ckb69jXynh5HuXzPy9TojX/mSkfO+oF+/bOSCbxi54hJvBnq/vkbqr9Sv6tUDjUwcr/ez0Avmdja6nYm+6Hq9/ylGpjUamTXTyOSJRsbpfUzS713H2T5dzxdiuXX9O4pdXcI5IiIiIiIiYrt1BRjEtnQF47add4uRFSu9meQ2mNtAbpdjSX71w7mdcW7XNx8+3MjXvmLky+d5sfzL3TLR/POfM/Ilvfw89QvnGqnT7b76ZS+mf+2rRnp805uhPrHByGK931sW++F8oR7HFCMzpxuZPcubfT7Wn6U+fqKRySVZwsX1fCFWq67XDawWCeeIiIiIiIiIBemKIojt0RWM83vrCiPr1nqzzFPh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crY2mhPOAQAAagP7mu56vXfrev9QrK73MO3QD+fReE44R0REREREbLfhAVYlrZVoTjgHAACoDVKv667Xfbeu9xHF6novU6Sp91L+e6vUey3COSIiIiIiYrsMh+xKWr5obnUNSgvVNSjONhjNrQAAAFD9BF/bXa//bl3vJ4rR9V6mHTriOeEcERERERGxXWYH7UqYieblCOeuAWmhugbD2QYH1Z5H/OE2AAAAVDPZr/Hu9wLZut5XFKPrPU07DIRzK+EcERERERGxaN1RuxJWazR3DaZtNCecAwAA1Abe63r2673rfUG2rvcXxeh6b1OkqfdW/nstwjkiIiIiImLRuqN2ua3FaD55IeEcAACgFki/tjte913vD7J1vc8oVNf7m3YYiOeEc0RERERExKJ0R+1KWJ5w7hp8Fqpr0BvWNXgORnPCOQAAQG2QfF2voXhOOEdERERERCxKd9Qut/Gbbe4a7GabPXD2B9R+NCecAwAA1Abp1/Zqj+eEc0RERERExGJ1R+1KGArnrkFeu3QNOAvVNdANmz1g9gfSgWhuBQAAgOon9PreafHc9X6nHRLOERERERERi9EdtctteWabuwabheoa4IbNHij7A+jgoNoXAAAAqp+s1/gqj+eEc0RERERExIJ1h+1yW/rZ5q5BZqG6BrZhswfI/sA5OqD2BQAAgOrH9RpfzfGccI6IiIiIiFiQ7qhdbuM129w1oA2bPTD2B8yuwbQvAAAAVD+u1/ikHYrnrvcjhep6D1S4hHNERERERMSCdIftcltNs82zB8T+QNk1iA4IAAAA1Y/rNT5tjnjuej+Rres9SSG63gcVLuEcERERERGxIN1hu9yWdra5a1BZqK6BbNjoYLiQaG4FAACA6sf1Gh+yyuI54RwREREREbFN3VG73MZntrlrABs2OghOD45dA+eQ7/rDbQAAAKhm3K/zEasonhPOERERERER29Qdtstttcw2jw5+i4nmhHMAAIDawHtdd73eR2x3PHe9RylE1/uitiWcIyIiIiIi5tUdtcttaLZ5p4Zz18A1Y3TQW2w0J5wDAADUBpnXdtfrfsQqiOeEc0RERERExLy6w3a5Le0yLa4BZCG6BqwZo4PdwqO5lXAOAABQSwRf292v/REd8dz1fiNb13uWQnS9R8ot4RwRERERETGv7rBdbks329w1cCxU12A1Y3Cg6+kP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">
            </p>

            <p>Note: Endpoints that have marked (Admin) have to be admin user to make a call to them.</p>
        </div>

        <div class="overflow-hidden content-section" id="content-get-user">
            <h2 id="get-user"><?php echo $this->lang->line('Get User'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_user_info/?api_key=api_key&user_id=user_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get user info you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_user_info'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "user_info": {
        "id": "39",
        "name": "test_username",
        "email": "test_user@test.com",
        "mobile": "01234567891",
        "address": "Heaven 21",
        "user_type": "Member",
        "status": "1",
        "add_date": "2020-09-27 08:13:01",
        "last_login_at": "0000-00-00 00:00:00",
        "expired_date": "2020-10-27 00:00:00",
        "package_id": "16",
        "last_login_ip": "127.0.0.1"
    }
}

Error Result Example:

{
    "status": "error",
    "message": "No Matching User Found"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>user_id</td>
                        <td>Integer</td>
                        <td>The ID of the user</td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td>String</td>
                        <td>The email address of the user</td>
                    </tr>
                </tbody>
            </table>
            <p>Use either user_id or email address to make a successful get-user-info API call</p>
        </div>

        <div class="overflow-hidden content-section" id="content-create-user">
            <h2 id="create-user"><?php echo $this->lang->line('Create User'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/create_system_user'; ?>' \
-d 'api_key=your_api_key' \
-d 'name=name' \
-d 'email=email' \
-d 'password=password' \
-d 'package_id=package_id' \
-d 'expired_date=expired_date'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To create a new user you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/create_system_user'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result example :

{ 
    "status": "success",
    "id": 29
}

Error Result Example:

{
    "status": "error",
    "message": "Email already exists"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key.</td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>String</td>
                        <td>Name of the user</td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td>String</td>
                        <td>Email of the user</td>
                    </tr>
                    <tr>
                        <td>password</td>
                        <td>String</td>
                        <td>Password of the user. We recommend use a combination of alpha-numeric characters including special characters.</td>
                    </tr>
                    <tr>
                        <td>package_id</td>
                        <td>Integer</td>
                        <td>Package ID of the system</td>
                    </tr>
                    <tr>
                        <td>expired_date</td>
                        <td>String</td>
                        <td>Expire Date in YYYY-MM-DD Format</td>
                    </tr>
                    <tr>
                        <td>mobile</td>
                        <td>Numeric</td>
                        <td>(optional) Mobile number of the user</td>
                    </tr>
                    <tr>
                        <td>address</td>
                        <td>String</td>
                        <td>(optional) Address of the user</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-update-user">
            <h2 id="update-user"><?php echo $this->lang->line('Update User'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/update_user/{userId}'; ?>' \
-d 'api_key=api_key' \
-d 'name=name' \
-d 'mobile=mobile' \
-d 'address=address' \
-d 'package_id=package_id' \
-d 'expired_date=expired_date' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To update a user you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/create_system_user/{userId}'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success"
}

Error Result Example:

{
    "status": "error",
    "message": "User not found with the ID: 12"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>String</td>
                        <td>(optional) The user's name to be updated</td>
                    </tr>
                    <tr>
                        <td>mobile</td>
                        <td>Numeric</td>
                        <td>(optional) The user's mobile number to be updated</td>
                    </tr>
                    <tr>
                        <td>address</td>
                        <td>String</td>
                        <td>(optional) The user's address number to be updated</td>
                    </tr>
                    <tr>
                        <td>package_id</td>
                        <td>Integer</td>
                        <td>(optional) The package_id to be updated</td>
                    </tr>
                    <tr>
                        <td>expired_date</td>
                        <td>Date</td>
                        <td>(optional) The expired_date of the user's validity to be updated:<br>
                            Date Format: 'YYYY-MM-DD'
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>You need to provide at least one more parameter with the api_key to make a successful update-user API call</p>
        </div>

        <div class="overflow-hidden content-section" id="content-get-all-packages">
            <h2 id="get-all-packages"><?php echo $this->lang->line('Get Packages'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_all_packages/?api_key=api_key'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get info of all pacakges, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_all_packages'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "packages": [
        {
            "id": "1",
            "package_name": "Trial",
            "module_ids": "251,80,79,263,65, ...",
            "price": "0",
            "validity": "0",
            "deleted": "0",
            "visible": "0",
            "highlight": "0"
        },
        {
            "id": "16",
            "package_name": "everything",
            "module_ids": "251,80,202,201,88,206, ...",
            "price": "112.35",
            "validity": "700",
            "deleted": "0",
            "visible": "1",
            "highlight": "0"
        }

        // more
    ]
}

Error Result Example:

{
    "status": "error",
    "message": "Either API Key Invalid or Member Validity Expired"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-get-subscriber">
            <h2 id="get-subscriber"><?php echo $this->lang->line('Get Subscriber'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/subscriber_information/?api_key=api_key&subscriber_id=subscriber_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get subscriber info, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/subscriber_information'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "subscriber_info": {
        "subscriber_id": "134242424",
        "labels": "",
        "first_name": "test_first_name",
        "last_name": "test_last_name",
        "full_name": "test_full_name",
        "gender": "",
        "locale": "",
        "timezone": "",
        "page_name": "XeroDevs",
        "page_id": "1123123123123",
        "unavailable": "0",
        "last_error_message": "",
        "refferer_source": "",
        "subscribed_at": "0000-00-00 00:00:00",
        "email": "test@test.com",
        "phone_number": "01234567891",
        "birthdate": "0000-00-00",
        "last_subscriber_interaction_time": "0000-00-00 00:00:00"
    }
}

Error Result Example:

{
    "status": "error",
    "message":" No Matching Subscriber Found"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>subscriber_id</td>
                        <td>Integer</td>
                        <td>Subscriber PSID</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-get-all-labels">
            <h2 id="get-all-labels"><?php echo $this->lang->line('Get Labels'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_all_labels/?api_key=api_key&page_id=page_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get all labels, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_all_labels'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "label_info":[
        {
            "label_id": "2472856209402188",
            "label_name": "Important"
        },
        {
            "label_id": "2328660457215897",
            "label_name": "Product"
        }
    ]
}

Error Result Example:

{
    "status": "error",
    "message": "No Matching Label Found"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>page_id</td>
                        <td>Integer</td>
                        <td>Page ID [must be bot enabled in the system]</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-create-label">
            <h2 id="create-label"><?php echo $this->lang->line('Create Label'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \ 
'<?php echo $endpoint . '/create_label'; ?>' \
-d 'api_key=api_key' \
-d 'label_name=label_name' \
-d 'page_id=page_id' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To create a label, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/create_label'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "label_id": "012345678790"
}

Error Result Example:

{
    "status": "error",
    "message": "No Enabled Bot Found"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>label_name</td>
                        <td>String</td>
                        <td>Label name to be created</td>
                    </tr>
                    <tr>
                        <td>page_id</td>
                        <td>Integer</td>
                        <td>Page ID [must be bot enabled in the system]</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-assign-label">
            <h2 id="assign-label"><?php echo $this->lang->line('Assign Label'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \ 
'<?php echo $endpoint . '/assign_label'; ?>' \
-d 'api_key=api_key' \
-d 'label_ids=6243946525492645,9452768492754035' \
-d 'page_id=page_id' \
-d 'subscriber_id=subscriber_id'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To assign a label, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/assign_label'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "success": true,
    "status": "success"
}

Error Result Example:

{
    "status": "error",
    "message": "(#100) The user ID is not valid."
}

{
    "status": "error",
    "message": "No Enabled Bot Found"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>label_ids</td>
                        <td>String</td>
                        <td>Label ID to be assigned. You can provide multiple label IDs separated by comma</td>
                    </tr>
                    <tr>
                        <td>page_id</td>
                        <td>Integer</td>
                        <td>Page ID [must be bot enabled in the system]</td>
                    </tr>
                    <tr>
                        <td>subscriber_id</td>
                        <td>Integer</td>
                        <td>Subscriber PSID</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-get-contact-group">
            <h2 id="get-contact-group"><?php echo $this->lang->line('Get Contact Group'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_contact_group/?api_key=api_key&contact_type_id=contact_type_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get contact group, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_contact_group'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "id": "4567",
    "user_id": "345",
    "type": "Group 12",
    "created_at": "2020-10-28 13:40:46",
    "deleted": "0"
}

Error Result Example:

{
    "status": "error",
    "message": "The contact type ID is required"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>contact_type_id</td>
                        <td>Integer</td>
                        <td>The contact type ID to be fetched</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-create-contact-group">
            <h2 id="create-contact-group"><?php echo $this->lang->line('Create Contact Group'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/create_contact_group'; ?>' \
-d 'api_key=api_key' \
-d 'name=name' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To create contact group, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/create_contact_group'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "contact_type_id": 276
}

Error Result Example:

{
    "status": "error",
    "message": "The contact type name is required"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>String</td>
                        <td>The name of contact group to be created</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-update-contact-group">
            <h2 id="update-contact-group"><?php echo $this->lang->line('Update Contact Group'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/update_contact_group/{contact_type_id}'; ?>' \
-d 'api_key=api_key' \
-d 'name=name' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To update contact group, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/update_contact_group/{contact_type_id}'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success"
}

Error Result Example:

{
    "status": "error",
    "message": "The contact type name is required"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>name</td>
                        <td>String</td>
                        <td>The name of the contact group to be updated</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-delete-contact-group">
            <h2 id="delete-contact-group"><?php echo $this->lang->line('Delete Contact '); ?>Group</h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/delete_contact_group'; ?>' \
-d 'api_key=api_key' \
-d 'contact_type_id=contact_type_id'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To delete contact group, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/delete_contact_group'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "message": "The contact type ID (2) has been deleted"
}

Error Result Example:

{
    "status": "error",
    "message": "The contact_type_id field is required"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>contact_type_id</td>
                        <td>Integer</td>
                        <td>The contact type ID to be deleted</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-get-contact">
            <h2 id="get-contact"><?php echo $this->lang->line('Get Contact'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_contact/?api_key=api_key&contact_id=contact_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get contact, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_contact'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "id": "233",
    "user_id": "87",
    "contact_type_id": "67",
    "first_name": "test_first_name",
    "last_name": "test_last_name",
    "phone_number": "01234567891",
    "email": "test@test.com",
    "notes": "",
    "unsubscribed": "0",
    "deleted": "0"
}

Error Result Example:

{
    "status": "error",
    "message": "The contact ID does not exist"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>contact_id</td>
                        <td>Integer</td>
                        <td>The contact ID to be fetched</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-create-contact">
            <h2 id="create-contact"><?php echo $this->lang->line('Create Contact'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/create_contact'; ?>' \
-d 'api_key=api_key' \
-d 'contact_type_id=contact_type_id' \
-d 'email=email' \
-d 'phone_number=phone_number' \
-d 'first_name=first_name' \
-d 'last_name=last_name' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To create contact, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/create_contact'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "new_contact_id": 45
}

Error Result Example:

{
    "status": "error",
    "message": "The email or phone_number is required"
}

{
    "status": "error",
    "message": "The contact ID does not exist"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>contact_type_id</td>
                        <td>Integer</td>
                        <td>The contact type ID to be attached to</td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td>String</td>
                        <td>The email address of the user</td>
                    </tr>
                    <tr>
                        <td>phone_number</td>
                        <td>String</td>
                        <td>The phone number of the user</td>
                    </tr>
                    </tr>
                    <tr>
                        <td>first_name</td>
                        <td>String</td>
                        <td>(optional) The first name of the user</td>
                    </tr>
                    <tr>
                        <td>last_name</td>
                        <td>String</td>
                        <td>(optional) The last name of the user</td>
                    </tr>
                </tbody>
            </table>
            <p>Note: Either email or phone_number is required</p>
        </div>

        <div class="overflow-hidden content-section" id="content-update-contact">
            <h2 id="update-contact"><?php echo $this->lang->line('Update Contact'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/update_contact/{contact_id}'; ?>' \
-d 'api_key=api_key' \
-d 'email=email' \
-d 'phone_number=phone_number' \
-d 'first_name=first_name' \
-d 'last_name=last_name' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To update contact, you need to make a POST call to the following endpoint'); ?> :<br>
                <code class="higlighted"><?php echo '/update_contact/{contact_id}'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success"
}

Error Result Example:

{
    "status": "error",
    "message": "You should at least provide value for first name or last name or email or phone number"
}

{
    "status": "error",
    "message": "The contact ID does not exist"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>contact_id</td>
                        <td>Integer</td>
                        <td>The contact ID to be updated for</td>
                    </tr>
                    <tr>
                        <td>email</td>
                        <td>String</td>
                        <td>(optional) The email address of the user</td>
                    </tr>
                    <tr>
                        <td>phone_number</td>
                        <td>String</td>
                        <td>(optional) The phone number of the user</td>
                    </tr>
                    </tr>
                    <tr>
                        <td>first_name</td>
                        <td>String</td>
                        <td>(optional) The first name of the user</td>
                    </tr>
                    <tr>
                        <td>last_name</td>
                        <td>String</td>
                        <td>(optional) The last name of the user</td>
                    </tr>
                </tbody>
            </table>
            <p>Note: You can update first_name or last_name. You can update email or phone_number if they have not been added previously.</p>
        </div>

        <div class="overflow-hidden content-section" id="content-delete-contact">
            <h2 id="delete-contact"><?php echo $this->lang->line('Delete Contact'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST \
'<?php echo $endpoint . '/delete_contact'; ?>' \
-d 'api_key=api_key' \
-d 'contact_id=contact_id' 
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To delete contact, you need to make a POST call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/delete_contact'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "status": "success",
    "message": "The contact ID (3) has been deleted"
}

Error Result Example:

{
    "status": "error",
    "message": "The contact ID does not exist"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>contact_id</td>
                        <td>Integer</td>
                        <td>The contact ID to be deleted</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-flow-campaign-list">
            <h2 id="flow-campaign-list"><?php echo $this->lang->line('Get Flow Campaign List'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_flow_campaigns/?api_key=api_key'; ?>
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get flow campaign list, you need to make a GET call to the following endpoint'); ?> :<br>
                <code class="higlighted"><?php echo '/get_flow_campaigns'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

[
    {
        "id": "1",
        "flow_name": "Flow Campaign 1"
    },
    {
        "id": "2",
        "flow_name": "Flow Campaign 2"
    },
    {
        "id": "3",
        "flow_name": "Flow Campaign 3"
    },
    {
        "id": "6",
        "flow_name": "Flow Campaign 4"
    }
]
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-flow-campaign-info">
            <h2 id="flow-campaign-info"><?php echo $this->lang->line('Get FLow Campaign Info'); ?></h2>
            <pre>
                <code class="bash">
curl -X POST '<?php echo $endpoint . '/get_flow_campaign_info/?api_key=api_key&flow_campaign_id=flow_campaign_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get flow campaign info, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_flow_campaign_info'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "id": "1",
    "flow_name": "serial check",
    "questions": [
        "What do you like to do?",
        "What is your favorite pet?",
        "What is your favorite color?",
        "Who is your favorite author?",
        "Which country do you love to live in?"
    ]
}

Error Result Example:

{
    "status": "error",
    "message": "The flow_campaign_id field is required"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>flow_campaign_id</td>
                        <td>Integer</td>
                        <td>The ID of the flow campaign</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-single-subscriber-flow-info">
            <h2 id="single-subscriber-flow-info"><?php echo $this->lang->line('Get Single Subscriber Flow Info'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_single_subscriber_flow_info/?api_key=api_key&flow_campaign_id=flow_campaign_id&subscriber_id=subscriber_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get single subscriber flow info, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_single_subscriber_flow_info'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "id": "1",
    "flow_name": "Flow Campaign 1",
    "data": [
        {
            "question_id": "45",
            "question": "What do you like to do?",
            "answer": "Reading, Traveling, Watching Movies"
        },
        {
            "question_id": "46",
            "question": "What is your favorite pet?",
            "answer": "Dog"
        },
        {
            "question_id": "47",
            "question": "What is your favorite color?",
            "answer": "Black"
        },
        {
            "question_id": "48",
            "question": "Who is your favorite author?",
            "answer": "Sigmund Freud"
        },
        {
            "question_id": "49",
            "question": "Which country do you love to live in?",
            "answer": "France"
        }
    ]
}

Error Result Example:

{
    "status": "error",
    "message": "The flow campaign ID does not exist"
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>flow_campaign_id</td>
                        <td>Integer</td>
                        <td>The ID of the flow campaign</td>
                    </tr>
                    <tr>
                        <td>subscriber_id</td>
                        <td>Integer</td>
                        <td>The ID of the flow campaign subscriber</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="overflow-hidden content-section" id="content-all-subscribers-flow-info">
            <h2 id="all-subscribers-flow-info"><?php echo $this->lang->line('Get All Subscribers Flow Info'); ?></h2>
            <pre>
                <code class="bash">
curl -X GET '<?php echo $endpoint . '/get_all_subscriber_flow_info/?api_key=api_key&flow_campaign_id=flow_campaign_id'; ?>'
                </code>
            </pre>
            <p>
                <?php echo $this->lang->line('To get all subscribers flow info, you need to make a GET call to the following endpoint'); ?>:<br>
                <code class="higlighted"><?php echo '/get_all_subscriber_flow_info'; ?></code>
            </p>
            <br>
            <pre>
                <code class="json">
Success Result Example:

{
    "id": "1",
    "flow_name": "serial check",
    "data": {
        "2499454673490220": [
            {
                "question_id": "45",
                "question": "What do you like to do?",
                "answer": "Reading, Traveling, Watching Movies"
            },
            {
                "question_id": "46",
                "question": "What is your favorite pet?",
                "answer": "Dog"
            },
            {
                "question_id": "47",
                "question": "What is your favorite color?",
                "answer": "Black"
            },
            {
                "question_id": "48",
                "question": "Who is your favorite author?",
                "answer": "Sigmund Freud"
            },
            {
                "question_id": "49",
                "question": "Which country do you love to live in?",
                "answer": "France"
            }
        ],
        "3001440154747507": [
            {
                "question_id": "45",
                "question": "What do you like to do?",
                "answer": "Traveling, Watching Movies"
            },
            {
                "question_id": "46",
                "question": "What is your favorite pet?",
                "answer": "Cat"
            },
            {
                "question_id": "47",
                "question": "What is your favorite color?",
                "answer": "Blue"
            },
            {
                "question_id": "48",
                "question": "Who is your favorite author?",
                "answer": "William Shakespeare"
            },
            {
                "question_id": "49",
                "question": "Which country do you love to live in?",
                "answer": "Germany"
            }
        ]
    }
}
                </code>
            </pre>
            <h4>PARAMETERS</h4>
            <table>
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>api_key</td>
                        <td>String</td>
                        <td>Your API key</td>
                    </tr>
                    <tr>
                        <td>flow_campaign_id</td>
                        <td>Integer</td>
                        <td>The ID of the flow campaign</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div class="content-code"></div>
</div>
<script src="<?php echo base_url('plugins/api/js/script.js'); ?>"></script>
</body>
</html>
<!--
 API Documentation HTML Template  - 1.0.1
 Copyright © 2016 Florian Nicolas
 Licensed under the MIT license.
 https://github.com/ticlekiwi/API-Documentation-HTML-Template
 !-->