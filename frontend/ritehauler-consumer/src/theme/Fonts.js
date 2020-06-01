import Metrics from "./Metrics";

const type = {
  base: "SFProText-Regular",
  bold: "SFProText-Bold",
  italic: "SFProText-Italic",
  boldItalic: "SFProText-BoldItalic",
  heavy: "SFProText-Heavy",
  heavyItalic: "SFProText-HeavyItalic",
  light: "SFProText-Light",
  lightItalic: "SFProText-LightItalic",
  medium: "SFProText-Medium",
  mediumItalic: "SFProText-MediumItalic",
  semiBold: "SFProText-Semibold",
  semiBoldItalic: "SFProText-SemiboldItalic",
  base2: "SFProDisplay-Regular",
  black: "SFProDisplay-Black",
  bold2: "SFProDisplay-Bold",
  heavy2: "SFProDisplay-Heavy",
  light2: "SFProDisplay-Light",
  medium2: "SFProDisplay-Medium",
  semiBold2: "SFProDisplay-Semibold",
  thin2: "SFProDisplay-Thin",
  ultraLight2: "SFProDisplay-Ultralight",
  ultrathinItalic2: "SFProDisplay-UltralightItalic",
  blackItalic2: "SFProDisplay-BlackItalic",
  boldItalic2: "SFProDisplay-BoldItalic",
  heavyItalic2: "SFProDisplay-HeavyItalic",
  italic2: "SFProDisplay-Italic",
  lightItalic2: "SFProDisplay-LightItalic",
  mediumItalic2: "SFProDisplay-MediumItalic",
  semiBoldItalic2: "SFProDisplay-SemiboldItalic",
  thinItalic2: "SFProDisplay-ThinItalic"
};

const size = {
  xxxxxSmall: Metrics.generatedFontSize(9),
  xxxxSmall: Metrics.generatedFontSize(11),
  xxxSmall: Metrics.generatedFontSize(12),
  xxSmall: Metrics.generatedFontSize(13),
  xSmall: Metrics.generatedFontSize(14),
  small: Metrics.generatedFontSize(15),
  normal: Metrics.generatedFontSize(16),
  normalBig: Metrics.generatedFontSize(17),
  medium: Metrics.generatedFontSize(18),
  large: Metrics.generatedFontSize(20),
  largeBig: Metrics.generatedFontSize(22),
  xLarge: Metrics.generatedFontSize(24),
  xxLarge: Metrics.generatedFontSize(26),
  xxxLarge: Metrics.generatedFontSize(30),
  xxxxLarge: Metrics.generatedFontSize(40),
  xxxxxLarge: Metrics.generatedFontSize(50),
  xxxxxxLarge: Metrics.generatedFontSize(60)
};

export default {
  type,
  size
};
