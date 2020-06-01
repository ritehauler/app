// @flow
import { StyleSheet, Platform } from "react-native";
import { Metrics, Fonts, Colors } from "../theme";

export default StyleSheet.create({
  container: {
    flex: 1
  },
  header: {
    backgroundColor: Colors.navbar.background,
    elevation: 0,
    borderBottomWidth: Metrics.ratio(1),
    borderBottomColor: Colors.background.login,
    shadowColor: Colors.transparent
  },
  loginHeader: {
    backgroundColor: Colors.navbar.background,
    elevation: 0,
    borderBottomWidth: 0,
    borderBottomColor: Colors.background.login,
    shadowColor: Colors.transparent
  },
  title: {
    // TODO: To center title on android add bellow styles
    // paddingHorizontal: Platform.OS === "ios" ? 70 : 56,
    // textAlign: Platform.OS === 'ios' ? 'center' : 'left',
    marginHorizontal: 16,
    fontSize: Fonts.size.large,
    fontFamily: Fonts.type.dBold,
    color: Colors.navbar.text,
    fontWeight:'bold'
  }
});
