<?php $LINK = "timeline"; ?>
<?php require_once("./addons/Session.php"); ?>
<?php
$ALL_PARCELS = listAllParcels();
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
              <h1 class="text-2xl md:text-3xl font-semibold">Generate Timeline</h1>
            </div>
          </div>
          <?php if (!count($ALL_PARCELS)) : ?>
            <div class="col-span-full bg-white h-56 flex items-center flex-col justify-center pb-8 text-center">
              <p class="text-xl mb-4 text-gray-400">No parcel found</p>
              <p class="text-sm text-gray-500">Please create a parcel to continue</p>
              <a href="create-parcel" class="text-sm flex mt-1 text-indigo-500 hover:text-indigo-600">Create a parcel</a>
            </div>
          <?php else : ?>
            <div class="col-span-full bg-white shadow-lg p-6 pb-8 rounded-sm border border-gray-200">
              <form action="./handler/timeline.handler.php" method="POST">
                <div class="grid grid-cols-12 w-full gap-4">
                  <div class="col-span-full sm:col-span-6">
                    <label for="title" class="mb-1 font-bold flex text-xs text-gray-500">Parcel *</label>
                    <select name="parcel" class="form-input w-full" id="">
                      <option value="" selected disabled>Select Parcel</option>
                      <?php foreach ($ALL_PARCELS as $parcel) : ?>
                        <option value="<?= $parcel['id'] ?>">
                          <?= $parcel['id'] . " - " . $parcel['title']; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="message" class="mb-1 font-bold flex text-xs text-gray-500">Message *</label>
                    <input type="text" name="message" id="message" placeholder="eg. OUT FOR DELIVERY " class="form-input w-full" required>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="location" class="mb-1 font-bold flex text-xs text-gray-500">Location *</label>
                    <input type="text" name="location" id="location" placeholder="eg: CINCINNATI, OH" class="form-input w-full" required>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="date" class="mb-1 font-bold flex text-xs text-gray-500">Date *</label>
                    <input type="datetime-local" name="date" id="date" placeholder="eg: 19x21x1 in." class="form-input w-full" required>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="date" class="mb-1 font-bold flex text-xs text-gray-500">Options * </label>
                    <div class="flex gap-6 items-center py-4">
                      <!-- FINAL SWITCH -->
                      <div class="mb-2">
                        <div class="flex mb-3">
                          <h3 class="text-xs font-semibold">Final Timeline</h3>
                          <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" class="block ml-2" aria-haspopup="true" :aria-expanded="open" @focus="open = true" @focusout="open = false" @click.prevent="" aria-expanded="false">
                              <svg class="w-4 h-4 fill-current text-gray-400" viewBox="0 0 16 16">
                                <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"></path>
                              </svg>
                            </button>
                            <div class="z-10 absolute bottom-full left-1/2 transform -translate-x-1/2">
                              <div class="min-w-56 bg-gray-800 p-2 rounded overflow-hidden mb-2" x-show="open" x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-out duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                                <div class="text-xs text-gray-200">When on, it means that the parcel have reached it's destination</div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="flex items-center" x-data="{ checked: false }">
                          <div class="form-switch">
                            <input type="checkbox" name="final" id="final" class="sr-only" x-model="checked" value="1">
                            <label class="bg-gray-400" for="final">
                              <span class="bg-white shadow-sm" aria-hidden="true"></span>
                              <span class="sr-only">Switch label</span>
                            </label>
                          </div>
                          <div class="text-sm text-gray-400 italic ml-2" x-text="checked ? 'On' : 'Off'">On</div>
                        </div>
                      </div>

                      <!-- IS SUMMARY -->
                      <div class="mb-2">
                        <div class="flex mb-3">
                          <h3 class="text-xs font-semibold">Summary Timeline</h3>
                          <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                            <button type="button" class="block ml-2" aria-haspopup="true" :aria-expanded="open" @focus="open = true" @focusout="open = false" @click.prevent="" aria-expanded="false">
                              <svg class="w-4 h-4 fill-current text-gray-400" viewBox="0 0 16 16">
                                <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"></path>
                              </svg>
                            </button>
                            <div class="z-10 absolute bottom-full left-1/2 transform -translate-x-1/2">
                              <div class="min-w-56 bg-gray-800 p-2 rounded overflow-hidden mb-2" x-show="open" x-transition:enter="transition ease-out duration-200 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-out duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
                                <div class="text-xs text-gray-200">
                                  When off, it means that this timelime will be used as (`more details` type of timeline). Summary timelines appear summary section.
                                  <div class="flex flex-col gap-2 mt-1">
                                    <a target="_blank" href="./assets/detail-timeline.png" class="text-indigo-400 flex">Details timeline example
                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9e9e9e" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none" /> <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5" /> <line x1="10" y1="14" x2="20" y2="4" /> <polyline points="15 4 20 4 20 9" /> </svg>
                                    </a>
                                    <a target="_blank" href="./assets/summary-timeline.png" class="text-indigo-400 flex">Summary timeline example
                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-external-link" width="16" height="16" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9e9e9e" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none" /> <path d="M11 7h-5a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-5" /> <line x1="10" y1="14" x2="20" y2="4" /> <polyline points="15 4 20 4 20 9" /> </svg>
                                    </a>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="flex items-center" x-data="{ checked: true }">
                          <div class="form-switch">
                            <input type="checkbox" name="summary" id="summary" class="sr-only" x-model="checked" value="1">
                            <label class="bg-gray-400" for="summary">
                              <span class="bg-white shadow-sm" aria-hidden="true"></span>
                              <span class="sr-only">Switch label</span>
                            </label>
                          </div>
                          <div class="text-sm text-gray-400 italic ml-2" x-text="checked ? 'On' : 'Off'">On</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-6">
                  <button name="add-timeline" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                      <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                    </svg>
                    <span class="ml-2">Add Timeline</span>
                  </button>
                </div>
              </form>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>
  <script src="./assets/js/main.75545896273710c7378c.js"></script>
  <script src="./assets/js/functions.js"></script>
</body>

</html>