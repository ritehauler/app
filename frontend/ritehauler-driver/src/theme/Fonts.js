// @flow
import Metrics from "./Metrics";
import Utils from "../util";

const type = {
  bold: "SFProText-Bold",
  base: "SFProText-Regular",
  medium: "SFProText-Medium",
  regular: "SFProText-Regular",
  tBold: "SFProText-Bold",
  sBold: "SFProText-Semibold",
  dBold: "SFProDisplay-Bold", 
  light: "SFProText-Light",
  dLight: "SFProDisplay-Light"
};

// Metrics.generatedFontSize(ios, android)
// const size = {
//   xxxxxSmall: Metrics.generatedFontSize(9),
//   xxxxSmall: Metrics.generatedFontSize(11),
//   xxxSmall: Metrics.generatedFontSize(12),
//   xxSmall: Metrics.generatedFontSize(13),
//   xSmall: Metrics.generatedFontSize(14),
//   small: Metrics.generatedFontSize(15),
//   normal: Metrics.generatedFontSize(16),
//   normalBig: Metrics.generatedFontSize(17),
//   medium: Metrics.generatedFontSize(18),
//   large: Metrics.generatedFontSize(20),
//   largeBig: Metrics.generatedFontSize(21),
//   xLarge: Metrics.generatedFontSize(24),
//   xxLarge: Metrics.generatedFontSize(26),
//   xxxLarge: Metrics.generatedFontSize(30),
//   xxxLLarge: Metrics.generatedFontSize(35),
//   xxxxLarge: Metrics.generatedFontSize(40),
//   xxxxxLarge: Metrics.generatedFontSize(50),
//   xxxxxxLarge: Metrics.generatedFontSize(60)
// };

const size = {
  xxxxxSmall: 9,
  xxxxSmall: 11,
  xxxSmall: 12,
  xxSmall: 13,
  xSmall: 14,
  small: 15,
  normal: 16,
  normalBig: 17,
  medium: 18,
  large: 20,
  largeBig: 21,
  xLarge: 24,
  xxLarge: 26,
  xxxLarge: 30,
  xxxLLarge: 35,
  xxxxLarge: 40,
  xxxxxLarge: 50,
  xxxxxxLarge: 60
};

export default {
  type,
  size
};
