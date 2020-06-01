import { Platform } from "react-native";

const white = "#FFFFFF";
const tuna = "#48494d";
const black = "#000000";
const grey = Platform.select({
  ios: "#F4F4F4",
  android: "#FAFAFA"
});
const transparent = "rgba(0,0,0,0)";

const primary = white;
const secondary = white;
const tertiary = black;
const quaternary = grey;

const loader = "rgba(128,128,128,0.2)";

const accent = "#ff5a5a";
const accent2 = "#ff8c5e";
const accent3 = "#4fbe9e";
const error = "#ff1919";
const info = "#0e82ff";

const background = {
  primary,
  secondary: "#f2f2f2",
  tertiary: "#00000057",
  accent: "#ff5a5a",
  semi_alpha_accent: "#e7635480",
  login: "#f4f4f4",
  backgroundSelect: "#ff8c5e",
  backgroundSelect2: "#ff5a5a",
  backgroundOwnChat: "#f7f7f7",
  underlineColor: "#48494d",
  progressBackgroundColor: "#F37D70",
  progressFillColor: "#F7A59B",
  routeColor: "#4fbc9d80",
  backgroundCircleView: "#e1e2e9",
  error,
  info
};

const text = {
  primary: "#000000",
  secondary: "#8e8e8e",
  tertiary: "#ffffff",
  quaternary: "#bebebe",
  accent: "#ff5a5a",
  accent2: "#4fbe9e",
  manatee: "#9a9da6",
  placeholder: "#c3c5d2",
  orange: "#f25f5c",
  productDetail: "#fed576",
  paymentLabel: "#babdc2",
  sectionLabel: "#818289",
  orderDepot: "#a5a7b4",
  timerColor: "#cfd0d0",
  navBarText: "#48494d",
  pending: "#e5241d"
};

const switchTint = "#ff8c5e";

const status = {
  assigned: "#ffb400",
  accepted: "#E65100",
  completed: "#54a204",
  cancelled: "#ff0000",
  declined: "#CDDC39",
  payRequired: "#0e82ff",
  reached: "#EC407A",
  arrived: "#9C27B0"
};

const navbar = {
  background: background.primary,
  text: text.primary
};

const border = "#e7e7e7";
const separator = "#eef1f7";
const separator2 = "#edeef2";

const lgColArray = ["#ff5a5a", "#ff8c5e"];
const lgGreyArray = ["#ededed", "#ededed"];
const lgDarkGreyArray = ["#bebebe", "#bebebe"];
const lgHalfGreyArray = ["#ff5a5a", "#ff8c5e", "#ededed"];

const uploadImage = "#c5c5c5";

export default {
  white,
  tuna,
  black,
  grey,
  transparent,
  primary,
  secondary,
  tertiary,
  quaternary,

  background,
  navbar,
  text,
  border,
  separator,
  separator2,

  lgColArray,
  lgGreyArray,
  lgHalfGreyArray,
  lgDarkGreyArray,

  switchTint,
  accent,
  accent2,
  accent3,

  status,

  uploadImage,
  loader,
  error
};
