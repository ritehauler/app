import React, { PureComponent } from "react";
import { Modal, View } from "react-native";
import Utils from "../../util";
import { Metrics } from "../../theme";

class MyModal extends PureComponent {
  state = {
    modalVisible: false
  };

  setModalVisible(visible) {
    this.setState({ modalVisible: visible });
  }

  render() {
    return (
      <Modal
        animationType="fade"
        transparent={true}
        onRequestClose={() => this.setModalVisible(false)}
        visible={this.state.modalVisible}
      >
        {this.props.children}
      </Modal>
    );
  }
}

export default MyModal;
