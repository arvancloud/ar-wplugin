<h1>دامنه: <?php echo $_GET['domain']; ?></h1>
<?php 
require_once('requests.php');
if (isset($_POST['action'])) {
    ArRequests::changeCachingStatus($_GET['domain'], $_POST['action']);
}
if (isset($_GET['purge'])) {
    if ($_GET['purge'] == 1)
        ArRequests::purgeAll($_GET['domain']);
}
?>
<link rel="stylesheet" href="<?php echo plugin_dir_url(__DIR__);?>assets/ar-wplugin.css">

<div class="ar-content p-cdnCacheSetting__row">
    <div class="p-cdnCacheSetting__box">
        <div class="p-cdnCacheSetting__boxTitle">
            <div class="p-cdnCacheSetting__cacheLevelTitle">
                سطح عملیات Cache
                <a href="https://www.arvancloud.com/help/fa/article/360033727554-تنظیمات-و-سطوح-عملیات-Caching"
                    target="_blank" rel="noopener noreferrer" class="ar-help ar-help--row">
                    <div class="ar-help__border ar-help__border--left" style=""></div> <svg
                        xmlns="http://www.w3.org/2000/svg" width="7.311" height="17.582" viewBox="0 0 7.311 17.582"
                        class="ar-help__icon">
                        <path
                            d="M14.271,18.307q-.912.36-1.455.548a3.838,3.838,0,0,1-1.262.189A2.515,2.515,0,0,1,9.837,18.5a1.739,1.739,0,0,1-.611-1.367,4.966,4.966,0,0,1,.045-.659c.031-.224.08-.476.147-.759l.761-2.688c.067-.258.125-.5.171-.731a3.241,3.241,0,0,0,.068-.633,1.589,1.589,0,0,0-1.958-1.48q.747-.3,1.43-.521a4.225,4.225,0,0,1,1.29-.218,2.468,2.468,0,0,1,1.692.53,1.761,1.761,0,0,1,.594,1.376q0,.175-.041.617a4.129,4.129,0,0,1-.152.811l-.757,2.68c-.062.215-.117.461-.167.736a3.892,3.892,0,0,0-.073.626C12.229,17.646,12.658,18.908,14.271,18.307ZM12.816,5.848a1.807,1.807,0,0,1-1.275.492,1.826,1.826,0,0,1-1.28-.492,1.571,1.571,0,0,1-.533-1.193,1.586,1.586,0,0,1,.533-1.2,1.812,1.812,0,0,1,1.28-.5,1.792,1.792,0,0,1,1.275.5,1.611,1.611,0,0,1,0,2.389Z"
                            transform="translate(-7.71 -2.212)" fill="none" stroke="currentColor" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="1.5"></path>
                    </svg>
                    <div class="ar-help__text" style=""><span class="ar-help__span" style="">
                            راهنما
                        </span></div>
                    <div class="ar-help__border ar-help__border--right"></div>
                </a></div>
        </div>
        <!---->
        <div class="p-cdnCacheSetting__boxContent">
            <div>
                <div class="p-cdnCacheSetting__cacheLevelMessage">
                    با انتخاب سطح عملیات کش میتوانید تعیین کنید که چه محتوایی از وبسایت شما در آروان کش شود
                </div>
                <div class="ar-row no-gutters p-cdnCacheSetting__row">
                    
                    <?php getCachedOptions(); ?>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getCachedOptions() {
    $settings = json_decode(ArRequests::getCachingSettings($_GET['domain']));
    
    $options = [
        [
            'id'        =>  'c-off',
            'title'     =>  'خاموش',
            'desc'      =>  'هیچگونه محتوایی سمت آروان کش نمیشود. درخواست ها مستقیم به سمت سرورهای اصلی ارسال میشود',
            'icon'      =>  '
                            <svg xmlns="http://www.w3.org/2000/svg" width="36.693" height="36.868" viewBox="0 0 36.693 36.868" class="p-cdnCacheSetting__modeButtonIcon p-cdnCacheSetting__modeButtonIcon--auto"><g transform="translate(5.34 12.639)" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><circle cx="11.74" cy="11.74" fill="rgba(124,232,221,0.47)" r="11.74"></circle> <path fill="none" d="M11.74 11.74L3.757 3.132"></path></g> <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.982.769l.098 3.892M23.765 1.108l-1.319 4.314M30.476 2.891l-3.04 4.783M31.424 11.044l4.21-3.793M10.539 2.465l1.205 2.949M5.113 5.517l1.702 2.1M1.044 10.264l1.473 1.029"></path></svg>
            '
        ],
        [
            'id'        =>  'c-uri',
            'title'     =>  'بدون QueryString',
            'desc'      =>  'در صورتی که URL یک فایل شامل Query String های مختلفی باشد تنها یک نسخه از آن فایل در آروان کش میشود',
            'icon'      =>  '
            <svg xmlns="http://www.w3.org/2000/svg" width="36.435" height="30.551" viewBox="0 0 36.435 30.551" class="p-cdnCacheSetting__modeButtonIcon p-cdnCacheSetting__modeButtonIcon--auto"><g transform="translate(0.75 0.75)"><g><path d="M406.385,925.374c4.374-4.081,8.42-8.561,14.5-9.918a12.647,12.647,0,0,1,2.736-.278,17.4,17.4,0,0,1,8.06,1.929,23.745,23.745,0,0,1,5.68,4.446c.325.327,3.88,3.821,3.954,3.746a48.766,48.766,0,0,1-7.578,6.952,18.46,18.46,0,0,1-10.116,3.2,17.3,17.3,0,0,1-8.709-2.337c-2.91-1.7-5.067-4.367-7.454-6.693Z" transform="translate(-406.385 -910.877)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path> <ellipse cx="7.889" cy="7.889" rx="7.889" ry="7.889" transform="translate(9.578 6.608)" fill="rgba(124,232,221,0.53)" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></ellipse> <path d="M452.374,931.158a5.44,5.44,0,1,1-5.44,5.44" transform="translate(-434.865 -922.102)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path> <ellipse cx="1.839" cy="1.839" rx="1.839" ry="1.839" transform="translate(15.628 12.658)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></ellipse> <path d="M420.6,900.921h-4.291V905.1" transform="translate(-413.357 -900.864)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path> <path d="M503.874,905.019v-4.291H499.7" transform="translate(-471.926 -900.729)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path> <path d="M499.313,988.292H503.6v-4.176" transform="translate(-471.656 -959.298)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path> <path d="M416.311,983.923v4.291h4.176" transform="translate(-413.357 -959.163)" fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path></g></g></svg>
            '
        ],
        [
            'id'        =>  'c-query_string',
            'title'     =>  'با QueryString',
            'desc'      =>  'در صورتی که Query String ها متفاوت باشند نسخه های مختلفی از فایل ها سمت آروان کش میشود',
            'icon'      =>  '
            <svg xmlns="http://www.w3.org/2000/svg" width="32.143" height="32.218" class="p-cdnCacheSetting__modeButtonIcon p-cdnCacheSetting__modeButtonIcon--auto"><g stroke="#2f2f2f" stroke-linecap="round" stroke-width="1.5"><g transform="translate(4.775 10.906)"><circle cx="10.195" cy="10.195" transform="translate(0 .171)" fill="rgba(124,232,221,0.63)" stroke-linejoin="round" r="10.195"></circle> <path d="M10.195 10.366V0" fill="none"></path></g> <g fill="none" stroke-linejoin="round"><path d="M14.886.769l.085 3.38"></path> <path d="M20.776 1.064L19.631 4.81"></path> <path d="M26.604 2.612l-2.64 4.153"></path> <path d="M27.427 9.693l3.656-3.294"></path> <path d="M9.29 2.242l1.047 2.561"></path> <path d="M4.578 4.892l1.478 1.824"></path> <path d="M1.044 9.015l1.279.894"></path></g></g></svg>
            '
        ],
        [
            'id'        =>  'c-advance',
            'title'     =>  'با QueryString + Cookie',
            'desc'      =>  'در کنار Query String مقادیر Cookie ها را هم در کش کردن فایل ها در نظر میگیرد',
            'icon'      =>  '
            <svg xmlns="http://www.w3.org/2000/svg" width="36.693" height="36.868" viewBox="0 0 36.693 36.868" class="p-cdnCacheSetting__modeButtonIcon p-cdnCacheSetting__modeButtonIcon--auto"><g transform="translate(5.34 12.639)" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><circle cx="11.74" cy="11.74" fill="rgba(124,232,221,0.51)" r="11.74"></circle> <path fill="none" d="M11.74 11.739l7.501-9.03"></path></g> <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.982.769l.098 3.892M23.765 1.108l-1.319 4.314M30.476 2.891l-3.04 4.783M31.424 11.044l4.21-3.793M10.539 2.465l1.205 2.949M5.113 5.517l1.702 2.1M1.044 10.264l1.473 1.029"></path></svg>
            '
        ]
    ];

    foreach ($options as $option) {
        if ('c-' . $settings->data->cache_status == $option['id']) {
            ?>
            <div class="p-cdnCacheSetting__modeButton" id="<?php echo $option['id']; ?>">
                <div
                    class="c-cdnModeButton c-cdnModeButton--column c-cdnModeButton--active p-cdnCacheSetting__modeButtonComp">
                    <div class="c-cdnModeButton__icon"><?php echo $option['icon']; ?></div>
                    <div class="c-cdnModeButton__content">
                        <div class="c-cdnModeButton__title">
                            <?php echo $option['title']; ?>
                        </div>
                        <div class="c-cdnModeButton__text">
                        <?php echo $option['desc']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }else {
            ?> 
            <div class="p-cdnCacheSetting__modeButton" id="<?php echo $option['id']; ?>">
                <div class="c-cdnModeButton c-cdnModeButton--column p-cdnCacheSetting__modeButtonComp">
                    <div class="c-cdnModeButton__icon"><?php echo $option['icon']; ?></div>
                    <div class="c-cdnModeButton__content">
                        <div class="c-cdnModeButton__title">
                        <?php echo $option['title']; ?>
                        </div>
                        <div class="c-cdnModeButton__text">
                        <?php echo $option['desc']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

}
?>
<script>
var c_off = document.getElementById("c-off");

c_off.style.cursor = 'pointer';
c_off.onclick = function() {
    var form = document.createElement("form");
    var element1 = document.createElement("input"); 

    form.method = "POST";
    form.action = "";   

    element1.value="off";
    element1.name="action";
    form.appendChild(element1);  


    document.body.appendChild(form);

    form.submit();
};

var c_uri = document.getElementById('c-uri');

c_uri.style.cursor = 'pointer';
c_uri.onclick = function() {
    var form = document.createElement("form");
    var element1 = document.createElement("input"); 

    form.method = "POST";
    form.action = "";   

    element1.value="uri";
    element1.name="action";
    form.appendChild(element1);  


    document.body.appendChild(form);

    form.submit();
};


var query_string = document.getElementById('c-query_string');

query_string.style.cursor = 'pointer';
query_string.onclick = function() {
    var form = document.createElement("form");
    var element1 = document.createElement("input"); 

    form.method = "POST";
    form.action = "";   

    element1.value="query_string";
    element1.name="action";
    form.appendChild(element1);  


    document.body.appendChild(form);

    form.submit();
};


var advance = document.getElementById('c-advance');

advance.style.cursor = 'pointer';
advance.onclick = function() {
    var form = document.createElement("form");
    var element1 = document.createElement("input"); 

    form.method = "POST";
    form.action = "";   

    element1.value="advance";
    element1.name="action";
    form.appendChild(element1);  


    document.body.appendChild(form);

    form.submit();
};

</script>

<div class="card">
    <h2 class="title">حذف اطلاعات Cache شده</h2>
    <p>
    پاک‌سازی فایل‌های Cache شده می‌تواند به‌طور موقت سرعت وب‌سایت شما را کاهش دهد.
</p>
<a href="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&purge=1" ?>" style="color:red;"> پاک کردن همه چیز</a>
</div>