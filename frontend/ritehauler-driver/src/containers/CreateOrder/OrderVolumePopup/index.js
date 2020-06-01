import React from "react";
import { Text, StyleSheet, View, TouchableWithoutFeedback } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import { ButtonView } from "../../../components";
import { Colors, Metrics, ApplicationStyles } from "../../../theme";
import { BottomButton } from "../../../components";

const OrderVolumePopup = ({ closeModal }) => {
  return (
    <ButtonView style={styles.container} onPress={() => closeModal(false)}>
      <View style={styles.messageWrapper}>
        <LinearGradient
          colors={Colors.lgColArray}
          start={{ x: 0.0, y: 0 }}
          end={{ x: 0.8, y: 0 }}
          style={[
            {
              flexDirection: "row",
              height: Metrics.bottomButtonHeight,
              alignItems: "center"
            }
          ]}
        >
          <Text style={[ApplicationStyles.dBoldW20, styles.title]}>Error</Text>
        </LinearGradient>

        <Text style={[ApplicationStyles.re15Gray, styles.messageText]}>
          You have added too many extra items for the vehicle to carry delete
          some extra items to successfully verify items volume with vehicle
          volume.
        </Text>
      </View>
    </ButtonView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.7)",
    alignItems: "center",
    justifyContent: "center"
  },
  messageWrapper: {
    marginHorizontal: Metrics.baseMargin,
    backgroundColor: Colors.background.primary
  },
  messageText: { padding: Metrics.baseMargin },
  title: {
    paddingHorizontal: Metrics.baseMargin,
    paddingTop: Metrics.baseMargin
  }
});

export default OrderVolumePopup;
