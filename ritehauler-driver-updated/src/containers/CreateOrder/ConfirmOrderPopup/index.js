import React from "react";
import { Text, StyleSheet, View } from "react-native";
import Modal from "react-native-modal";
import LinearGradient from "react-native-linear-gradient";
import { ButtonView } from "../../../components";
import { Colors, Metrics, ApplicationStyles } from "../../../theme";
import { BottomButton } from "../../../components";

class ConfirmOrderPopup extends React.PureComponent {
  state = {
    modalVisible: false
  };

  setModalVisible(visible) {
    this.setState({ modalVisible: visible });
  }
  render() {
    return (
      <Modal
        //animationType="fade"
        //transparent={true}
        //onRequestClose={() => this.setModalVisible(false)}
        //visible={this.state.modalVisible}

        isVisible={this.state.modalVisible}
        style={styles.modal}
        animationIn="fadeIn"
        animationOut="fadeOut"
        onBackdropPress={() => this.setModalVisible(false)}
        onBackButtonPress={() => this.setModalVisible(false)}
      >
        <ButtonView
          style={styles.container}
          onPress={() => this.setState({ modalVisible: false })}
        >
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
              <Text style={[ApplicationStyles.dBoldW20, styles.title]}>
                Success
              </Text>
            </LinearGradient>

            <Text style={[ApplicationStyles.re15Gray, styles.messageText]}>
              Extra items volume successfully verified with truck volume you can
              now proceed.
            </Text>
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
              <BottomButton title="Done" onPress={() => alert("executed")} />
            </LinearGradient>
          </View>
        </ButtonView>
      </Modal>
    );
  }
}

const styles = StyleSheet.create({
  modal: {
    margin: 0
  },
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

export default ConfirmOrderPopup;
