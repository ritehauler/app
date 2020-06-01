import React, { Component } from "react";
import { StyleSheet, Keyboard } from "react-native";

const WithKeyboardSubscription = (compRef, onKeyboardVisible) => {
  compRef.keyboardDidShowListener = Keyboard.addListener(
    "keyboardDidShow",
    () =>
      compRef.setState(
        {
          isKeyboardVisible: true
        },
        () => (onKeyboardVisible ? onKeyboardVisible() : null)
      )
  );
  compRef.keyboardDidHideListener = Keyboard.addListener(
    "keyboardDidHide",
    () =>
      compRef.setState({
        isKeyboardVisible: false
      })
  );
};
const WithKeyboardUnSubscription = compRef => {
  compRef.keyboardDidShowListener.remove();
  compRef.keyboardDidHideListener.remove();
};

export { WithKeyboardSubscription, WithKeyboardUnSubscription };
