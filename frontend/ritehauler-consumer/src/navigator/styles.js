// @flow
import { StyleSheet, Platform } from "react-native";
import { Fonts, Colors, Metrics } from "../theme";

export default StyleSheet.create({
  headerLogin: {
    backgroundColor: Colors.background.secondary,
    elevation: 0,
    borderBottomWidth: 0,
    marginTop: Platform.select({
      ios: 0,
      android: Platform.Version >= 19 ? Metrics.statusBarHeight : 0
    })
  },
  headerDashBoard: {
    backgroundColor: Colors.background.secondary,
    elevation: 1,
    borderBottomWidth: 1,
    borderBottomColor: Colors.background.separator,
    marginTop: Platform.select({
      ios: 0,
      android: Platform.Version >= 19 ? Metrics.statusBarHeight : 0
    })
  },
  title: {
    fontSize: Fonts.size.large,
    fontFamily: Fonts.type.bold2,
    color: Colors.text.primary
  }
});
