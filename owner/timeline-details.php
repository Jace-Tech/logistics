<?php $LINK = "timeline"; ?>
<?php require_once("./addons/Session.php"); ?>
<?php if (!isset($_GET['parcel_id'])) redirect($_SERVER['HTTP_REFERER'] ?? "./view-timeline"); ?>
<?php
$PARCEL_TIMELINES = listParcelTimelines($_GET['parcel_id']);

$SUMMARY_TIMELINES = array_values(array_filter($PARCEL_TIMELINES, function ($timeline) {
  return $timeline['is_summary'];
}));

$DETAILS_TIMELINES = array_values(array_filter($PARCEL_TIMELINES, function ($timeline) {
  return !$timeline['is_summary'];
}));

?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>ADMIN OCP - Timeline</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="./assets/css/style.311cc0a03ae53c54945b.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/date/jquery.datetimepicker.min.css">
  <script src="./assets/js/jquery.js"></script>
</head>

<body class="font-inter antialiased bg-gray-100 text-gray-600" :class="{ 'sidebar-expanded': sidebarExpanded }" x-data="{ page: 'dashboard', sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }" x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))">
  <script>
    localStorage.setItem('sidebar-expanded', 'true')
  </script>
  <script>
    if (localStorage.getItem('sidebar-expanded') == 'true') {
      document.querySelector('body').classList.add('sidebar-expanded');
    } else {
      document.querySelector('body').classList.remove('sidebar-expanded');
    }
  </script>
  <div class="flex h-screen overflow-hidden">
    <?php include("./inc/Sidebar.php") ?>
    <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
      <?php include("./inc/Header.php"); ?>
      <main>
        <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
          <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
              <h1 class="text-2xl md:text-3xl font-semibold">Timeline Details</h1>
            </div>
          </div>
          <div class="col-span-full bg-white shadow-lg p-6 pb-8 rounded-sm border border-gray-200">
            <h2 class="text-lg mb-8 font-medium">Summary Timelines</h2>
            <div class="p-5 border-gray-200" style="border-left-width: 1px;">
              <?php foreach ($SUMMARY_TIMELINES as $index => $timeline) : ?>
                <div id="<?= $timeline['id'] ?>" class="relative <?= (count($SUMMARY_TIMELINES) - 1 !== $index) ? "mb-8" : "" ?>">
                  <div class="rounded-full flex items-center justify-center bg-gray-200 absolute" style=" width: fit-content; min-width: 28px; min-height: 28px; top: 0; left: -34.5px;">
                    <?php if($timeline['is_delivered']): ?>
                      <svg class="fill-current shrink-0" width="11" height="8" viewBox="0 0 11 8">
                        <path d="m.457 4.516.969-.99 2.516 2.481L9.266.702l.985.99-6.309 6.284z"></path>
                      </svg>
                    <?php endif; ?>
                  </div>
                  <div class="max-w-sm ml-4 p-4 rounded shadow-md border border-gray-100 flex flex-col">
                    <p class="text-xs text-gray-500 tracking-widest font-semibold uppercase"><?= $timeline['location'] ?></p>
                    <h2 class="text-md mt-1 text-indigo-600 font-medium"><?= $timeline['message'] ?></h2>
                    <p class="text-gray-400 text-xs"><?= date("M jS, Y h:i a", strtotime($timeline['date'])) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="col-span-full bg-white mt-8 shadow-lg p-6 pb-8 rounded-sm border border-gray-200">
            <h2 class="text-lg mb-8 font-medium">Details Timelines</h2>
            <div class="p-5 border-gray-200" style="border-left-width: 1px;">
              <?php foreach ($DETAILS_TIMELINES as $index => $timeline) : ?>
                <div id="<?= $timeline['id'] ?>" class="relative <?= (count($DETAILS_TIMELINES) - 1 !== $index) ? "mb-8" : ""  ?>">
                  <div class="rounded-full flex items-center justify-center bg-gray-200 absolute" style=" width: fit-content; min-width: 28px; min-height: 28px; top: 0; left: -4%;">
                    <?php if($timeline['is_delivered']): ?>
                      <svg class="fill-current shrink-0" width="11" height="8" viewBox="0 0 11 8">
                        <path d="m.457 4.516.969-.99 2.516 2.481L9.266.702l.985.99-6.309 6.284z"></path>
                      </svg>
                    <?php endif; ?>
                  </div>
                  <div class="max-w-sm ml-4 p-4 rounded shadow-md border border-gray-100 flex flex-col">
                    <p class="text-xs text-gray-500 tracking-widest font-semibold uppercase"><?= $timeline['location'] ?></p>
                    <h2 class="text-md mt-1 text-indigo-600 font-medium"><?= $timeline['message'] ?></h2>
                    <p class="text-gray-400 text-xs"><?= date("M jS, Y h:i a", strtotime($timeline['date'])) ?></p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <script src="./assets/js/main.75545896273710c7378c.js"></script>
  <script src="./assets/js/functions.js"></script>
</body>

</html>