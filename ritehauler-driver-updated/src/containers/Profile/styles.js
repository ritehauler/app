import { StyleSheet } from "react-native";
import { Colors, Metrics } from "../../theme";

export default StyleSheet.create({
  containerStyle: {
    flex: 1,
    backgroundColor: Colors.background.login
  },
  userImageContainerStyle: {
    height: Metrics.profileImageSize + Metrics.doubleBaseMargin
  },
  userImageContainerView1Style: {
    backgroundColor: Colors.background.accent,
    flex: 1
  },
  userImageContainerView2Style: {
    backgroundColor: Colors.background.login,
    flex: 1
  },
  imageContainerStyle: {
    position: "absolute",
    top: 0,
    bottom: 0,
    left: 0,
    right: 0,
    alignItems: "center",
    justifyContent: "center"
  },
  imageStyle: {
    width: Metrics.profileImageSize,
    height: Metrics.profileImageSize,
    borderRadius: Metrics.profileImageRadius
  },
  userDetailContainerStyle: {
    paddingVertical: Metrics.baseMargin,
    paddingHorizontal: Metrics.baseMargin
  },
  userNameContainerStyle: {
    flexDirection: "row"
  },
  firstNameStyle: {
    flex: 1,
    marginRight: Metrics.ratio(5)
  },
  lastNameStyle: {
    flex: 1,
    marginLeft: Metrics.ratio(5)
  }
});
