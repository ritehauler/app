// @flow
import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { View } from 'react-native';
import Swiper from 'react-native-swiper';

import Page from './Page';
import styles from './styles';

export default class Tutorial extends Component {
	static propTypes = {
		data: PropTypes.array.isRequired,
		renderPage: PropTypes.func
	};
	static defaultProps = {
		renderPage: (item, index) => <Page key={index} data={item} />
	};

	state = {
		width: undefined,
		height: undefined
	};

	getRef = () => this.ref;

	ref = undefined;

	renderContent(data: Object, renderPage: Function) {
		return data.map(renderPage);
	}

	render() {
		const { width, height } = this.state;
		const { data, renderPage, ...rest } = this.props;
		return (
			<View
				style={styles.container}
				onLayout={({ nativeEvent }) =>
					this.setState({
						width: nativeEvent.layout.width,
						height: nativeEvent.layout.height
					})}
			>
				{width &&
				height && (
					<Swiper ref={(ref) => (this.ref = ref)} {...rest} width={width} height={height}>
						{this.renderContent(data, renderPage)}
					</Swiper>
				)}
			</View>
		);
	}
}
