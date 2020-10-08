// // @flow
import { StyleSheet } from "react-native";
import Colors from "./Colors";
import Fonts from "./Fonts";

export default StyleSheet.create({
  flex: {
    flex: 1
  },
  messageBarTitle: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.medium,
    fontSize: Fonts.size.medium
  },
  messageBarDescription: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.light,
    fontSize: Fonts.size.normal
  },
  dBold16: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.dBold,
    fontSize: Fonts.size.normal
  },
  tBold16: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.tBold,
    fontSize: Fonts.size.normal
  },
  dBold26: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.dBold,
    fontSize: Fonts.size.xxLarge
  },
  dBoldW20: {
    color: Colors.text.tertiary,
    fontFamily: Fonts.type.dBold,
    fontSize: Fonts.size.xxLarge
  },
  dBoldB20: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.dBold,
    fontSize: Fonts.size.xxLarge
  },
  re13Black: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.xxSmall
  },
  re14Black: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.xSmall
  },
  re16Black: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.normal
  },
  re16BlackAccount: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.xSmall,
    marginTop:15
  },
  re16White: {
    color: Colors.text.tertiary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.small
  },
  re15Gray: {
    color: Colors.text.secondary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.small
  },
  re13Gray: {
    color: Colors.text.secondary,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.xxSmall
  },
  sB16Black: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.sBold,
    fontSize: Fonts.size.normal
  },
  sB14Black: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.sBold,
    fontSize: Fonts.size.xSmall
  },
  sB14Grey: {
    color: Colors.text.quaternary,
    fontFamily: Fonts.type.sBold,
    fontSize: Fonts.size.xSmall
  },
  sb14Orange: {
    color: Colors.accent2,
    fontFamily: Fonts.type.sBold,
    fontSize: Fonts.size.xSmall
  },
  dLight12Grey: {
    color: Colors.text.quaternary,
    fontFamily: Fonts.type.dLight,
    fontSize: Fonts.size.xxxSmall
  },
  lGrey13: {
    color: Colors.text.tertiary,
    fontFamily: Fonts.type.light,
    fontSize: Fonts.size.xxSmall
  },
  lBlack14: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.light,
    fontSize: Fonts.size.xSmall
  },
  lBlack16: {
    color: Colors.text.primary,
    fontFamily: Fonts.type.light,
    fontSize: Fonts.size.normal
  },
  dLight14Orange: {
    color: Colors.accent2,
    fontFamily: Fonts.type.dLight,
    fontSize: Fonts.size.xSmall
  },
  re16Orange: {
    color: Colors.accent2,
    fontFamily: Fonts.type.regular,
    fontSize: Fonts.size.normal
  }
});
