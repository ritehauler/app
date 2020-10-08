import React, { PureComponent } from "react";
import { Modal, View, Animated, Easing } from "react-native";
import Utils from "../../util";
import { Metrics, Colors } from "../../theme";

class MyModal extends PureComponent {
  constructor(props) {
    super(props);

    this.scaleAnimation = new Animated.Value(1);
    this.opacityAnimation = new Animated.Value(0);
  }

  state = {
    modalVisible: false
  };

  animateAppearance() {
    Animated.stagger(300, [
      Animated.timing(
        this.scaleAnimation, // The value to drive
        {
          toValue: 50,
          duration: 1500,
          easing: Easing.bezier(0.73, 0.06, 0.05, 0.44),
          useNativeDriver: true
        }
      ),
      Animated.timing(this.opacityAnimation, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true
      })
    ]).start();
  }

  setModalVisible(visible) {
    if (visible) {
      this.setState({ modalVisible: visible }, () => {
        this.animateAppearance();
      });
    } else {
      this.setState({
        modalVisible: visible
      });
    }
  }

  render() {
    return (
      <Modal
        animationType="fade"
        transparent={true}
        onRequestClose={() => this.setModalVisible(false)}
        visible={this.state.modalVisible}
      >
        <Animated.View
          style={[
            {
              width: 50,
              height: 50,
              borderRadius: 25,
              position: "absolute",
              top: 0,
              right: 0,
              backgroundColor: Colors.background.secondary
            },
            {
              transform: [
                {
                  scale: this.scaleAnimation
                }
              ]
            }
          ]}
        />
        <Animated.View style={{ flex: 1, opacity: this.opacityAnimation }}>
          {this.props.children}
        </Animated.View>
      </Modal>
    );
  }
}

export default MyModal;
