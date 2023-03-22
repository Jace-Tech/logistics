<?php $LINK = "timeline"; ?>
<?php require_once("./addons/Session.php"); ?>

<?php
if (!isset($_GET['timeline_id'])) redirect($_SERVER['HTTP_REFERER']);
$TIMELINE_ID = $_GET['timeline_id'];
$TIMELINE_DETAILS = getTimeline($TIMELINE_ID);

print_r($TIMELINE_DETAILS);
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>ADMIN OCP - Edit Timeline</title>
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
            <div class="mb-4 sm:mb-0 flex gap-4 items-center">

              <div x-data="{ modalOpen: false }">
                <button class="btn hover:bg-white border-gray-200 hover:border-gray-300" @click.prevent="modalOpen = true" aria-controls="info-modal">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9e9e9e" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <line x1="5" y1="12" x2="11" y2="18" />
                    <line x1="5" y1="12" x2="11" y2="6" />
                  </svg>
                </button>
                <div class="fixed inset-0 bg-gray-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true" style="display: none;"></div>
                <div id="info-modal" class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center transform px-4 sm:px-6" role="dialog" aria-modal="true" x-show="modalOpen" x-transition:enter="transition ease-in-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" style="display: none;">
                  <div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full" @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                    <div class="p-5 flex space-x-4">
                      <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-indigo-100">
                        <svg class="w-4 h-4 shrink-0 fill-current text-indigo-500" viewBox="0 0 16 16">
                          <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z"></path>
                        </svg>
                      </div>
                      <div>
                        <div class="mb-2">
                          <div class="text-lg font-semibold text-gray-800">You have unsaved changes?</div>
                        </div>
                        <div class="text-sm mb-10">
                          <div class="space-y-2">
                            <p>Are you sure you wanna leave the page?</p>
                          </div>
                        </div>
                        <div class="flex flex-wrap justify-end space-x-2">
                          <button class="btn-sm border-gray-200 hover:border-gray-300 text-gray-600" @click="modalOpen = false">Cancel</button>
                          <a href="<?= $_SERVER['HTTP_REFERER']; ?>" class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Yes, Leave</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <h1 class="text-2xl md:text-3xl font-semibold">Edit Timeline ( ID: <?= $TIMELINE_ID ?> )</h1>
            </div>
          </div>
          <div class="col-span-full bg-white shadow-lg p-6 pb-8 rounded-sm border border-gray-200">
              <form action="./handler/timeline.handler.php" method="POST">
                <div class="grid grid-cols-12 w-full gap-4">
                  <div class="col-span-full sm:col-span-6">
                    <label for="message" class="mb-1 font-bold flex text-xs text-gray-500">Message *</label>
                    <input type="text" name="message" value="<?= $TIMELINE_DETAILS['message']; ?>"  id="message" placeholder="eg. OUT FOR DELIVERY " class="form-input w-full" required>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="location" class="mb-1 font-bold flex text-xs text-gray-500">Location *</label>
                    <input type="text" name="location" id="location" value="<?= $TIMELINE_DETAILS['location']; ?>" placeholder="eg: CINCINNATI, OH" class="form-input w-full" required>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="date" class="mb-1 font-bold flex text-xs text-gray-500">Date *</label>
                    <input type="hidden" value="<?= $TIMELINE_DETAILS['parcel'] ?>" name="parcel_id">
                    <input type="datetime-local" name="date" id="date" value="<?= $TIMELINE_DETAILS['date']; ?>" placeholder="eg: 19x21x1 in." class="form-input w-full" required>
                  </div>

                  <div class="col-span-full sm:col-span-6">
                    <label for="date" class="mb-1 font-bold flex text-xs text-gray-500">Status *</label>
                    <select name="status" value="<?= $TIMELINE_DETAILS['status']; ?>" class="form-input w-full text-gray-500" id="">
                      <option value="" selected disabled>Choose timeline status</option>
                      <option value="delivered" <?= $TIMELINE_DETAILS['status'] == "delivered" ? "selected" : "" ?>>Delivered</option>
                      <option value="in transit" <?= $TIMELINE_DETAILS['status'] == "in transit" ? "selected" : "" ?>> In Transit</option>
                      <option value="not delivered" <?= $TIMELINE_DETAILS['status'] == "not delivered" ? "selected" : "" ?>>Not Delivered</option>
                    </select>
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
                        <div class="flex items-center" x-data="{ checked: <?= $TIMELINE_DETAILS['is_delivered'] == 1 ? 'true' : 'false' ?> }">
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
                        <div class="flex items-center" x-data="{ checked: <?= $TIMELINE_DETAILS['is_summary'] == 1 ? 'true' : 'false' ?> }">
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
                  <button name="edit-timeline" value="<?= $TIMELINE_DETAILS['id']; ?>" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                    <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                      <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path>
                    </svg>
                    <span class="ml-2">Updated Timeline</span>
                  </button>
                </div>
              </form>
            </div>
        </div>
      </main>
    </div>
  </div>
  <script src="./assets/js/main.75545896273710c7378c.js"></script>
  <script src="./assets/js/functions.js"></script>
  <script>
    $('#generator').click(() => {
      $('#id').val(generateID())
    })
  </script>
</body>

</html>