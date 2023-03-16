<div class="fixed z-10 max-w-md" style="top: 1rem; right: 1rem; z-index: 200;" >
  <?php if (isset($_SESSION['ADMIN_ALERT'])) :  ?>
    <?php $alert = json_decode($_SESSION['ADMIN_ALERT'], true); ?>

    <!-- SUCCESS ALERT -->
    <?php if (strtolower($alert['type']) === "success") : ?>
      <div x-show="open" x-data="{ open: true }">
        <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-green-500 text-white">
          <div class="flex w-full justify-between items-start">
            <div class="flex">
            <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z"></path></svg>
              <div class="font-medium"><?= $alert['message']; ?></div>
            </div><button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
              <div class="sr-only">Close</div><svg class="w-4 h-4 fill-current">
                <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- WARNING ALERT -->
    <?php elseif (strtolower($alert['type']) === "warning") : ?>
      <div x-show="open" x-data="{ open: true }">
        <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-yellow-500 text-white">
          <div class="flex w-full justify-between items-start">
            <div class="flex">
            <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"></path></svg>
              <div class="font-medium"><?= $alert['message']; ?></div>
            </div><button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
              <div class="sr-only">Close</div><svg class="w-4 h-4 fill-current">
                <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- INFO ALERT -->
    <?php elseif (strtolower($alert['type']) === "info") : ?>
      <div x-show="open" x-data="{ open: true }">
        <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-indigo-500 text-white">
          <div class="flex w-full justify-between items-start">
            <div class="flex">
            <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z"></path></svg>
              <div class="font-medium"><?= $alert['message']; ?></div>
            </div><button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
              <div class="sr-only">Close</div><svg class="w-4 h-4 fill-current">
                <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- ERROR ALERT -->
    <?php else : ?>
      <div x-show="open" x-data="{ open: true }">
        <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-red-500 text-white">
          <div class="flex w-full justify-between items-start">
            <div class="flex">
            <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path></svg>
              <div class="font-medium"><?= $alert['message']; ?></div>
            </div><button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
              <div class="sr-only">Close</div><svg class="w-4 h-4 fill-current">
                <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    <?php endif; ?>
    <!-- CLEAR ALERT SESSION AFTER SHOWING -->
    <?php unset($_SESSION['ADMIN_ALERT']); ?>
  <?php endif; ?>
</div>