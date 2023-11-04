<template>
  <div class="w-full">
    <div id="gjs" class="w-full"></div>
  </div>
</template>

<script setup>
import grapesjs from "grapesjs";
import "grapesjs/dist/css/grapes.min.css";
import "grapesjs/dist/grapes.min.js";
import grapesjsPresetWebpage from "grapesjs-preset-webpage";
import grapesjsBasic from "grapesjs-blocks-basic";
import grapesjsFlexbox from "grapesjs-blocks-flexbox";

const emit = defineEmits(["update"]);

onMounted(() => {
  const editor = grapesjs.init({
    container: "#gjs",
    height: "900px",
    width: "100%",
    plugins: [grapesjsPresetWebpage, grapesjsBasic, grapesjsFlexbox],
    storageManager: {
      id: "gjs-",
      type: "local",
      autosave: true,
      storeComponents: true,
      storeStyles: true,
      storeHtml: true,
      storeCss: true,
    },
    deviceManager: {
      devices: [
        {
          id: "desktop",
          name: "Desktop",
          width: "",
        },
        {
          id: "tablet",
          name: "Tablet",
          width: "768px",
          widthMedia: "992px",
        },
        {
          id: "mobilePortrait",
          name: "Mobile portrait",
          width: "320px",
          widthMedia: "575px",
        },
      ],
    },
    pluginsOpts: {
      "grapesjs-preset-webpage": {
        blocksBasicOpts: {
          blocks: [
            "column1",
            "column2",
            "column3",
            "column3-7",
            "text",
            "link",
            "image",
            "video",
          ],
          flexGrid: 1,
        },
        blocks: ["link-block", "quote", "text-basic", "link"],
      },
    },
  });
  editor.on("storage:store", () => {
    const htmlWithCss = editor
      .getHtml()
      .concat("<style>" + editor.getCss() + "</style>");

    emit("update", htmlWithCss);
  });
});
</script>
<style>
/* We can remove the border we've set at the beginnig */
body #gjs {
  border: none;
}
/* Theming */

/* Primary color for the background */
body .gjs-one-bg {
  background-color: #1f2937;
  border: 1px solid #374151;
}

/* Secondary color for the text color */
body .gjs-two-color {
  color: rgba(255, 255, 255, 0.7);
}

/* Tertiary color for the background */
body .gjs-three-bg {
  background-color: #1f2937;
  color: white;
}

/* Quaternary color for the text color */
body .gjs-four-color,
body .gjs-four-color-h:hover {
  color: #64748b;
}
</style>
