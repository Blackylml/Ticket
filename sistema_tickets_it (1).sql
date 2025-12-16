-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-07-2025 a las 16:32:09
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_tickets_it`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actividades_it`
--

CREATE TABLE `actividades_it` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin_estimada` date NOT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `duracion_estimada_dias` int(11) NOT NULL,
  `duracion_real_dias` int(11) DEFAULT NULL,
  `estado` enum('pendiente','en_progreso','completada','pausada','cancelada') DEFAULT 'pendiente',
  `prioridad` enum('baja','media','alta','critica') DEFAULT 'media',
  `asignado_a` int(11) DEFAULT NULL,
  `dependencia_actividad_id` int(11) DEFAULT NULL,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `horas_estimadas` decimal(8,2) DEFAULT NULL,
  `horas_trabajadas` decimal(8,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `actividades_it`
--

INSERT INTO `actividades_it` (`id`, `proyecto_id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin_estimada`, `fecha_fin_real`, `duracion_estimada_dias`, `duracion_real_dias`, `estado`, `prioridad`, `asignado_a`, `dependencia_actividad_id`, `progreso_porcentaje`, `horas_estimadas`, `horas_trabajadas`, `created_at`, `updated_at`) VALUES
(1, 1, 'Logica del programa', 'Programar el backend', '2025-06-09', '2025-06-23', NULL, 15, NULL, 'pendiente', 'media', 4, NULL, 0.00, 123.00, 0.00, '2025-06-09 03:40:45', '2025-06-09 03:40:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `archivos_adjuntos`
--

CREATE TABLE `archivos_adjuntos` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `nombre_archivo` varchar(255) NOT NULL,
  `ruta_archivo` varchar(500) NOT NULL,
  `tipo_archivo` varchar(50) DEFAULT NULL,
  `tamaño_archivo` int(11) DEFAULT NULL,
  `fecha_subida` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `archivos_adjuntos`
--

INSERT INTO `archivos_adjuntos` (`id`, `ticket_id`, `nombre_archivo`, `ruta_archivo`, `tipo_archivo`, `tamaño_archivo`, `fecha_subida`) VALUES
(5, 32, 'WhatsApp Image 2025-06-19 at 8.45.12 AM.jpeg', '32_1750344360_0.jpeg', 'jpeg', 250757, '2025-06-19 14:46:00'),
(6, 34, 'WhatsApp Image 2025-06-23 at 10.29.46 AM.jpeg', '34_1750696200_0.jpeg', 'jpeg', 190644, '2025-06-23 16:30:00'),
(7, 35, 'WhatsApp Image 2025-06-23 at 10.29.46 AM.jpeg', '35_1750696200_0.jpeg', 'jpeg', 190644, '2025-06-23 16:30:00'),
(8, 40, 'OT´s CERRADAS.png', '40_1751038782_0.png', 'png', 153683, '2025-06-27 15:39:42'),
(9, 43, 'editado.png', '43_1751307463_0.png', 'png', 7177, '2025-06-30 18:17:43'),
(10, 43, 'HH.png', '43_1751307463_1.png', 'png', 9723, '2025-06-30 18:17:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_ticket`
--

CREATE TABLE `comentarios_ticket` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `comentario` text NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hitos_proyecto`
--

CREATE TABLE `hitos_proyecto` (
  `id` int(11) NOT NULL,
  `proyecto_id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_objetivo` date NOT NULL,
  `fecha_completado` date DEFAULT NULL,
  `estado` enum('pendiente','completado','retrasado') DEFAULT 'pendiente',
  `es_critico` tinyint(1) DEFAULT 0,
  `actividades_requeridas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`actividades_requeridas`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos_it`
--

CREATE TABLE `proyectos_it` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin_estimada` date NOT NULL,
  `fecha_fin_real` date DEFAULT NULL,
  `estado` enum('planificado','en_progreso','pausado','completado','cancelado') DEFAULT 'planificado',
  `prioridad` enum('baja','media','alta','critica') DEFAULT 'media',
  `responsable_id` int(11) DEFAULT NULL,
  `progreso_porcentaje` decimal(5,2) DEFAULT 0.00,
  `presupuesto` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos_it`
--

INSERT INTO `proyectos_it` (`id`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin_estimada`, `fecha_fin_real`, `estado`, `prioridad`, `responsable_id`, `progreso_porcentaje`, `presupuesto`, `created_at`, `updated_at`) VALUES
(1, 'Timeline tracker', 'Crear un timeline para el tracker de ots con fechas compromiso ', '2025-06-09', '2025-09-09', NULL, 'planificado', 'alta', 4, 0.00, NULL, '2025-06-09 03:37:59', '2025-06-09 03:37:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos_actividades`
--

CREATE TABLE `recursos_actividades` (
  `id` int(11) NOT NULL,
  `actividad_id` int(11) NOT NULL,
  `tipo_recurso` enum('humano','hardware','software','presupuesto') NOT NULL,
  `nombre_recurso` varchar(255) NOT NULL,
  `cantidad_necesaria` decimal(10,2) DEFAULT 1.00,
  `cantidad_asignada` decimal(10,2) DEFAULT 0.00,
  `costo_unitario` decimal(10,2) DEFAULT NULL,
  `fecha_necesaria` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes_gantt`
--

CREATE TABLE `reportes_gantt` (
  `id` int(11) NOT NULL,
  `nombre_reporte` varchar(255) NOT NULL,
  `tipo_reporte` enum('proyecto_especifico','todos_proyectos','por_periodo','por_responsable') NOT NULL,
  `parametros` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parametros`)),
  `archivo_pdf` varchar(500) DEFAULT NULL,
  `generado_por` int(11) NOT NULL,
  `fecha_generacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_inicio_periodo` date DEFAULT NULL,
  `fecha_fin_periodo` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes_gantt`
--

INSERT INTO `reportes_gantt` (`id`, `nombre_reporte`, `tipo_reporte`, `parametros`, `archivo_pdf`, `generado_por`, `fecha_generacion`, `fecha_inicio_periodo`, `fecha_fin_periodo`) VALUES
(1, 'gantt_it_2025-06-09_05-38-46.pdf', '', NULL, 'gantt_it_2025-06-09_05-38-46.pdf', 4, '2025-06-09 03:38:46', NULL, NULL),
(2, 'gantt_it_2025-06-09_05-38-54.pdf', '', NULL, 'gantt_it_2025-06-09_05-38-54.pdf', 4, '2025-06-09 03:38:54', NULL, NULL),
(3, 'gantt_it_2025-06-09_05-39-03.pdf', '', NULL, 'gantt_it_2025-06-09_05-39-03.pdf', 4, '2025-06-09 03:39:03', NULL, NULL),
(4, 'gantt_it_2025-06-09_05-41-41.pdf', '', NULL, 'gantt_it_2025-06-09_05-41-41.pdf', 4, '2025-06-09 03:41:41', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `seguimiento_actividades`
--

CREATE TABLE `seguimiento_actividades` (
  `id` int(11) NOT NULL,
  `actividad_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_evento` enum('inicio','progreso','pausa','reanudacion','finalizacion','comentario') NOT NULL,
  `progreso_anterior` decimal(5,2) DEFAULT NULL,
  `progreso_nuevo` decimal(5,2) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `horas_trabajadas` decimal(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `numero_ficha` varchar(20) NOT NULL DEFAULT '',
  `solicitante_nombre` varchar(100) NOT NULL DEFAULT '',
  `titulo` varchar(200) NOT NULL,
  `descripcion` text NOT NULL,
  `prioridad` enum('baja','media','alta','urgente') DEFAULT 'media',
  `categoria` varchar(50) NOT NULL,
  `estado` enum('abierto','en_proceso','resuelto','cerrado') DEFAULT 'abierto',
  `solicitante_id` int(11) DEFAULT NULL,
  `asignado_a` int(11) DEFAULT NULL,
  `resolucion` text DEFAULT NULL,
  `satisfaccion` enum('satisfactoria','insatisfactoria','pendiente') DEFAULT NULL,
  `comentarios_cierre` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_asignacion` timestamp NULL DEFAULT NULL,
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `numero_ficha`, `solicitante_nombre`, `titulo`, `descripcion`, `prioridad`, `categoria`, `estado`, `solicitante_id`, `asignado_a`, `resolucion`, `satisfaccion`, `comentarios_cierre`, `fecha_creacion`, `fecha_asignacion`, `fecha_resolucion`, `fecha_cierre`) VALUES
(5, '3174', 'Michell Martínez Rodríguez', 'No enciende el CPU ', 'Buenos días, hace ratito el CPU de Impo-expo se apagó de la nada, ya he tratado de encenderla pero no pasa nada;(', 'alta', 'Red', 'cerrado', NULL, 5, 'Se reviso el regulador, se necesita cambio.', 'satisfactoria', NULL, '2025-06-10 16:36:02', '2025-06-10 16:40:57', '2025-06-10 16:49:18', '2025-06-10 16:51:45'),
(6, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'Me comentaron los supervisores que si por favor se puede agregar 6:10, 6:15 en las opciones de horas', 'media', 'Software', 'cerrado', NULL, 4, 'Se ajusto el codigo para añadir estas opciones\n', 'satisfactoria', NULL, '2025-06-10 21:21:17', '2025-06-10 21:24:49', '2025-06-10 21:26:31', '2025-06-10 21:27:10'),
(7, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'Corregir fecha del reporte #70 al 6 de junio y del reporte #91 al 9 de junio', 'media', 'Software', 'cerrado', NULL, 4, 'Se corrigio la fecha de los reportes', 'satisfactoria', NULL, '2025-06-10 21:35:02', '2025-06-10 21:35:38', '2025-06-10 21:36:20', '2025-06-10 21:38:35'),
(8, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'Cambiar fecha del reporte #102 al 5 de junio', 'media', 'Software', 'cerrado', NULL, 4, 'Se cambio la fecha del reporte\n', 'satisfactoria', NULL, '2025-06-10 23:29:25', '2025-06-10 23:45:53', '2025-06-10 23:47:37', '2025-06-11 13:21:47'),
(9, '3142', 'ilse noemi reyes guajardo', 'error en impresora', 'en estado de la impresora me dice desconocido', 'media', 'Impresoras', 'cerrado', NULL, 5, 'Se volvio a vincular impresora al dispositivo', 'satisfactoria', NULL, '2025-06-11 00:03:01', '2025-06-11 00:17:42', '2025-06-11 00:22:56', '2025-06-11 00:23:20'),
(10, '2905', 'Sergio Sánchez', 'Carpeta compartida ', 'la carpeta que tengo compartida no me quiere abrir ', 'alta', 'Red', 'cerrado', NULL, 5, 'Se volvio a vincular la carpeta\n', 'satisfactoria', NULL, '2025-06-11 15:12:28', '2025-06-11 15:20:58', '2025-06-11 15:50:37', '2025-06-11 15:50:50'),
(11, '1192', 'ELIZABETH MURILLO GARCIA', 'NO PUEDO IMPRIMIR DESDE MICROSIP', 'COMO ULTIMAMENTE A PASADO.. NO ME APARECE EN MICROSIP LA IMPRESORA.\r\n\r\nGRACIAS!', 'alta', 'Impresoras', 'cerrado', NULL, 4, 'Se vinculo la impresora\n', 'satisfactoria', NULL, '2025-06-11 16:13:40', '2025-06-11 16:37:06', '2025-06-11 16:43:59', '2025-06-11 16:51:59'),
(12, '2857', 'Mario Alberto Gomez Polendo', 'Carpeta Compartida', 'Crear una carpeta compartida con diferentes usuarios', 'alta', 'Software', 'cerrado', NULL, 5, 'Se creo carpeta y se vinculo con los usuarios deseados\n', 'satisfactoria', NULL, '2025-06-11 16:51:54', '2025-06-11 17:27:02', '2025-06-11 18:43:13', '2025-06-11 18:43:47'),
(13, '3185', 'Isaias Rios', 'Archivo de excel con problemas', 'Tengo un archivo de excel que no abre', 'media', 'Otro', 'cerrado', NULL, 4, 'Se solicito a la compañera reenviar el archivo\n', 'satisfactoria', NULL, '2025-06-11 19:20:34', '2025-06-11 19:20:44', '2025-06-11 19:21:03', '2025-06-11 19:21:14'),
(14, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'Corregir reporte 91 con fecha al 9 de junio\r\neliminar reporte 110 (hizo 2 del mismo día)\r\nreporte 70 corregir fecha al 6 de junio', 'media', 'Software', 'cerrado', NULL, 4, 'Se corrigieron los reportes, favor de mencionar que area abarca este reporte para mejor deteccion y evitar errores, gracias!\n', 'satisfactoria', NULL, '2025-06-11 21:31:57', '2025-06-11 21:44:40', '2025-06-11 21:46:51', '2025-06-11 21:47:55'),
(15, '3310', 'Isabel Milagro de Jesús Flores Cordova', 'Impresora', 'Se desconecta ', 'alta', 'Impresoras', 'cerrado', NULL, 4, 'Se reconfiguro la impresora\n', 'satisfactoria', NULL, '2025-06-12 15:33:59', '2025-06-12 16:05:50', '2025-06-12 16:06:01', '2025-06-12 17:28:43'),
(16, '870', 'Pedro Hernandez Sarabia', 'Sin acceso a carpeta compartida', 'Se intentó abrir una carpeta compartida donde se tiene la información de loa avances y estatus de loa materiales pero no me da acceso', 'alta', 'Software', 'cerrado', NULL, 5, 'Se volvio a vincular carpeta\n', 'satisfactoria', NULL, '2025-06-12 16:20:17', '2025-06-12 16:46:01', '2025-06-12 17:25:47', NULL),
(17, '3142', 'ilse noemi reyes guajardo', 'falla en tablet de vigilancia', 'error en autorizacion de pases en la tablet', 'alta', 'Red', 'cerrado', NULL, 4, 'Intermitencia en el internet\n', 'satisfactoria', NULL, '2025-06-12 16:47:40', '2025-06-12 16:49:30', '2025-06-12 17:26:32', '2025-06-12 17:27:21'),
(18, '2905', 'Sergio Sánchez', 'Carpeta compartida ', 'problemas con la carpeta compartida ', 'alta', 'Red', 'cerrado', NULL, 5, 'Se vinculo carpeta', 'satisfactoria', NULL, '2025-06-12 17:22:07', '2025-06-12 17:25:56', '2025-06-12 18:00:23', '2025-06-13 15:33:51'),
(19, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'Corregir fecha de reportes:\r\n107 subensambles nave 3 al 10 de junio\r\n106 subensambles nave 1 al 10 de junio', 'media', 'Software', 'cerrado', NULL, 4, 'Se corrigieron los reportes, gracias por poner la descripcion solicitada', 'satisfactoria', NULL, '2025-06-12 21:13:16', '2025-06-12 21:53:21', '2025-06-12 21:53:58', '2025-06-12 22:01:31'),
(20, '3185', 'Isaias Rios', 'tóner en impresora phaser 3020', 'Cambio de tóner en impresora ya que los formatos salen no muy visibles ', 'baja', 'Impresoras', 'cerrado', NULL, 4, 'Se cambio el toner de la impresora y se llevaron los residuos especiales al jefe de seguridad\n', 'satisfactoria', NULL, '2025-06-12 22:11:47', '2025-06-13 15:17:37', '2025-06-13 15:24:32', '2025-06-13 15:25:02'),
(21, '752', 'KARLA LETICIA MORALES GUEVARA', 'NO LLEGAN CORREOS', 'No están llegando los correos a la bandeja de entrada.. es un problema general en oficinas.\r\npor favor tu apoyo', 'alta', 'Email', 'cerrado', NULL, 4, 'La falla era general, era por parte del proveedor, revisando, el servicio regreso a la normalidad\n', 'satisfactoria', NULL, '2025-06-13 17:14:55', '2025-06-13 21:45:43', '2025-06-13 21:47:16', NULL),
(22, '1962', 'ZURISADAY LOPEZ NAVARRO', 'MICROSIP', 'INSTALAR EL SISTEMA DE MICROSIP A LAPTOP DE PRACTICANTES\r\nEN CASO DE SOLICITAR', 'baja', 'Software', 'cerrado', NULL, 4, 'Se agregara el microsip, faltaria revisar con el lic romo si se generara un usuario nuevo\n', 'satisfactoria', NULL, '2025-06-13 17:21:54', '2025-06-13 22:38:21', '2025-06-13 22:38:55', NULL),
(23, '1962', 'ZURISADAY LOPEZ NAVARRO', 'CONFIGURACION DE IMPRESORAS', 'CONFIGURAR COMPUTADORAS DE RH PARA IMPRIMIR', 'media', 'Software', 'cerrado', NULL, 5, 'Impresoras Configuradas\n', 'satisfactoria', NULL, '2025-06-13 18:18:50', '2025-06-13 21:47:36', '2025-06-13 22:35:06', NULL),
(24, '870', 'Pedro Hernandez Sarabia', 'sin acceso a sistema de RH', 'No puedo acceder al sistema de rh para solicitar cambios de turno ', 'alta', 'Software', 'cerrado', NULL, 4, 'el enlace es rhdigital.ct.ws, se reviso y la pagina esta en orden\n', 'satisfactoria', NULL, '2025-06-13 20:47:29', '2025-06-13 23:03:31', '2025-06-13 23:04:30', NULL),
(25, '870', 'Pedro Hernandez Sarabia', 'Sin acceso a carpeta compartida', 'No tengo acceso a la carpeta compartida de habilitado', 'alta', 'Software', 'cerrado', NULL, 5, 'Duda Resuelta', 'satisfactoria', NULL, '2025-06-16 13:04:42', '2025-06-16 14:21:10', '2025-06-16 14:25:22', NULL),
(26, '3185', 'Isaias Rios', 'Revisión de carpeta compartida ', 'Revisión de carpeta compartida, para poder modificar documentación de la CSH', 'media', 'Otro', 'cerrado', NULL, 4, 'se le indico al compañero que las solicitudes para cambio de archivos en el sgi sera por medio del sistema y ya no se dara acceso a la carpeta compartida mas que a la ing \nyessika palacios', 'satisfactoria', NULL, '2025-06-17 16:48:52', '2025-06-17 16:56:47', '2025-06-17 16:57:37', '2025-06-19 14:14:54'),
(27, '7', 'Sofia Izamar Villastrigo Mendoza', 'Excel ', 'No me deja abrir el Excel porque tiene uno la computadora que no está actualizado ', 'media', 'Otro', 'resuelto', NULL, 5, 'Software Actualizado', NULL, NULL, '2025-06-17 16:51:30', '2025-06-17 20:48:47', '2025-06-17 20:49:25', NULL),
(28, '7', 'Sofia Izamar Villastrigo Mendoza', 'Excel ', 'No me deja abrir el Excel porque tiene uno la computadora que no está actualizado ', 'media', 'Otro', 'resuelto', NULL, 5, 'Software Actualizado', NULL, NULL, '2025-06-17 16:51:30', '2025-06-17 20:48:53', '2025-06-17 20:49:29', NULL),
(29, '752', 'KARLA LETICIA MORALES GUEVARA', 'FALLA INTERNET', 'FALLA INTERNET.\r\nSE MUESTRA INTERMITENTE A NIVEL GENERAL DE OFICINAS', 'media', 'Red', 'abierto', NULL, NULL, NULL, NULL, NULL, '2025-06-18 23:15:17', NULL, NULL, NULL),
(30, '752', 'KARLA LETICIA MORALES GUEVARA', 'FALLA INTERNET', 'FALLA INTERNET.\r\nSE MUESTRA INTERMITENTE A NIVEL GENERAL DE OFICINAS', 'media', 'Red', 'abierto', NULL, NULL, NULL, NULL, NULL, '2025-06-18 23:15:17', NULL, NULL, NULL),
(31, '3185', 'Isaias Rios', 'Impresora ', 'No puedo imprimir desde mi computadora ', 'media', 'Impresoras', 'cerrado', NULL, 5, 'Impresora Configurada\n', 'satisfactoria', NULL, '2025-06-19 14:16:01', '2025-06-19 14:30:56', '2025-06-19 15:00:35', '2025-06-19 15:24:49'),
(32, '3174', 'Michell Martínez Rodríguez', 'No tengo internet', 'No me sale ninguna red disponible', 'alta', 'Red', 'cerrado', NULL, 5, 'Se configuro el adaptador inalambrico de vuelta', 'satisfactoria', NULL, '2025-06-19 14:46:00', '2025-06-19 15:01:43', '2025-06-19 15:08:41', '2025-06-19 15:27:53'),
(33, '719', 'José Ma. Vásquez Quintero', 'NERGY LINK', 'Buen día Marcos\r\n\r\nPodrías Crear el Dibujo 24181 del cliente Energy Link para subir la Ingeniería\r\n\r\n# Dibujo	              Nombre Común\r\n   24181	              1100 TEMPERING AIR SPOOL DUCT CASING', 'alta', 'Otro', 'cerrado', NULL, 4, 'Hola buenas tardes inge, el proceso de creacion del dibujo se hace cuando victor sube la ingenieria, nadamas asegurar que el ensamble general venga nombrado con el nombre comun, cualquier cosa estoy a disposicion\n', 'satisfactoria', NULL, '2025-06-20 16:34:32', '2025-06-20 21:13:04', '2025-06-20 21:13:50', '2025-06-21 13:43:39'),
(34, '3174', 'Michell Martínez Rodríguez', 'ERROR AL ABRIR APLICACION', 'Estoy tratando de abrir la aplicación de SACI y me aparece un error.', 'media', 'Software', 'cerrado', NULL, 4, 'Se resolvio incidencia con SASI\n', 'satisfactoria', NULL, '2025-06-23 16:30:00', '2025-06-23 16:41:02', '2025-06-23 16:41:17', '2025-06-23 23:09:44'),
(35, '3174', 'Michell Martínez Rodríguez', 'ERROR AL ABRIR APLICACION', 'Estoy tratando de abrir la aplicación de SACI y me aparece un error.', 'media', 'Software', 'cerrado', NULL, 4, 'Se resolvio incidencia con SASI\n', 'satisfactoria', NULL, '2025-06-23 16:30:00', '2025-06-23 16:41:00', '2025-06-23 16:41:35', '2025-06-23 23:09:39'),
(36, '3174', 'Michell Martínez Rodríguez', 'Conexión por ethernet', 'Buen día, solicito su apoyo para que mi equipo esté conectado a internet a través de ethernet', 'media', 'Red', 'cerrado', NULL, 5, 'Conexion por cable establecida\n', 'satisfactoria', NULL, '2025-06-24 18:24:30', '2025-06-24 18:35:19', '2025-06-24 18:35:38', '2025-06-24 18:55:59'),
(37, '3174', 'Michell Martínez Rodríguez', 'FIRMA MARIO PINEDA', 'Buenas tardes, solicito su apoyo con la firma para el correo electrónico de mi compañero de nuevo ingreso:\r\nNombre: Mario Pineda\r\nPuesto: Coordinador de Import - Export\r\nCorreo: imp-exp@famisa.mx\r\nGracias de antemano!!', 'baja', 'Otro', 'cerrado', NULL, 4, 'Se envio la firma a traves de whatsapp\n', 'satisfactoria', NULL, '2025-06-24 18:34:42', '2025-06-25 00:24:24', '2025-06-25 00:24:35', '2025-07-11 14:52:49'),
(38, '719', 'José Ma. Vásquez Quintero', 'CAMBIO DE NOMBRE COMUN', 'Víctor Garcia el día de ayer subió ingeniería de los equipos 13784 y 13787, no le puso el Nombre Común corto, ya le había explicado como debería subirlo y lo subió con el nombre del dibujo completo\r\n\r\nPueden cambiar por favor a nombre común corto:\r\n\r\n# Dibujo	Nombre Común\r\n13784	Support Exhaust\r\n13787	Support Exhaust\r\n\r\n', 'baja', 'Otro', 'cerrado', NULL, 4, 'Se cambio el nombre de los dos equipos a Support Exhaust, favor de corroborar con el Ing, Victor el nombre correcto de los equipos, para no alterar tanto el sistema por el back', 'satisfactoria', NULL, '2025-06-25 13:57:12', '2025-06-25 14:17:28', '2025-06-25 14:18:26', '2025-06-25 18:35:31'),
(39, '2368', 'JENNIFER DANIELA TOLEDO NUÑEZ', 'ELIMINACION DE PARTIDA DEN SISTEMA DE TRAZABILIDAD DE MATERIALES ', 'Hubo un error de captura en el  lote #12178 http://192.168.1.134/ReciboMtlCalidadDetalle , por el cual solicito la eliminación del mismo para volver a general la carga de ese lote correctamente desde el sistema de MATERIA PRIMA CALIDAD.', 'media', 'Software', 'cerrado', NULL, 4, 'Se elimino la partida, puedes proceder a cargarla en el sistema \n', 'satisfactoria', NULL, '2025-06-26 16:22:24', '2025-06-26 17:11:23', '2025-06-26 17:12:57', '2025-06-26 18:04:36'),
(40, '719', 'José Ma. Vásquez Quintero', 'OT´s TERMINADAS', 'Estaba revisando las OT´s y encontré varias que ya se embarcaron, me puedes dar acceso para darlas por terminadas porque ya hay demasiadas.\r\nTe envío un ejemplo las que tienen la flecha azul son algunas de las que aparecen y ya se embarcaron.', 'alta', 'Otro', 'cerrado', NULL, 4, 'Buenas tardes Inge, en el sistema no se pueden cerrar las ordenes a no ser que hayan pasado por TODO el proceso, la unica alternativa es hacer el proceso o borrarla del sistema, pero eso implica perder toda la trazabilidad de nesteos, liberaciones y habilitado,', 'insatisfactoria', NULL, '2025-06-27 15:39:42', '2025-06-27 23:35:54', '2025-06-27 23:37:00', '2025-07-01 13:57:35'),
(41, '719', 'José Ma. Vásquez Quintero', 'Correo para Karen Jocelin Ramos Sanchez', 'Necesitamos que le asignen un correo de la empresa a Karen', 'urgente', 'Red', 'cerrado', NULL, 5, 'Los correos no son asignados por IT, Por favor hablar con el Lic. Romo.', 'satisfactoria', NULL, '2025-06-30 14:19:50', '2025-06-30 14:21:16', '2025-06-30 14:21:44', '2025-07-01 13:56:48'),
(42, '2644', 'Vanessa Lizette Perez Estrada', 'Internet yesi galaviz ', 'Pueden ponerle internet a la lap de yesi, se encuentra en el departamento de finanzas ', 'alta', 'Red', 'resuelto', NULL, 5, 'Se realizara conexión por cable ', NULL, NULL, '2025-06-30 16:28:57', '2025-06-30 18:38:14', '2025-06-30 19:06:42', NULL),
(43, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'podrían cambiar la fecha del reporte #308 torres 30/40 para el día 26 de junio\r\nlo hice en el programa para editar, pero no se cambio en el otro ', 'media', 'Software', 'cerrado', NULL, 4, 'Se resolvio el cambio de fechas\n', 'satisfactoria', NULL, '2025-06-30 18:17:43', '2025-07-01 14:33:00', '2025-07-01 14:33:23', '2025-07-01 15:46:02'),
(44, '3298', 'Xitlaly Barajas', 'Áreas a agregar en sistema', 'Hola, podrían agregar al sistema de horas hombre las siguientes áreas por favor:\r\nAlmacén\r\nMaquinados\r\nMateriales\r\nMantenimiento', 'media', 'Software', 'cerrado', NULL, 4, 'Se agregaron las areas faltantes, espero tu retroalimentacion\n', 'satisfactoria', NULL, '2025-06-30 18:48:06', '2025-07-01 14:33:38', '2025-07-01 14:39:15', '2025-07-01 14:58:47'),
(45, '2368', 'JENNIFER DANIELA TOLEDO NUÑEZ', 'Instalacin de Softwer de una maquina de etiquetas de medicion', 'se compro una maquinas para colocar etiquetas adhesivas para los instrumentos de medición, pero solicita instalar una aplicación y no se si mi PC esta en condiciones de instalar esta aplicación ', 'media', 'Software', 'resuelto', NULL, 5, 'Se arreglo cartucho atascado, se llego a la conclucion de que la impresora es la que no funciona.', NULL, NULL, '2025-06-30 21:00:26', '2025-06-30 21:24:47', '2025-07-02 00:14:56', NULL),
(46, '3298', 'Xitlaly Barajas', 'Sistema de Horas Hombre', 'Podrían agregar 9 horas a las opciones de los reportes, es para el caso de los compañeros de almacén que trabajan en horario de oficinas', 'media', 'Software', 'cerrado', NULL, 4, 'Se agrego la opcion extra para las horas\n', 'satisfactoria', NULL, '2025-06-30 23:15:58', '2025-07-01 14:39:26', '2025-07-01 14:40:45', '2025-07-01 14:51:31'),
(47, '23688', 'JENNIFER DANIELA TOLEDO NUÑEZ', 'Instalacin de Softwer de una maquina de etiquetas de medicion', 'el dia de ayer el ing.gilberto me instalo un sistema para impresión de etiquetas de una maquina que se tenia disponible, quedo pendiente la impresión esta conectada pero no imprime no se si tenemos novedad o si el ingeniero pudo revisar algo de este punto, busque en el manual y según yo debería funcionar ', 'media', 'Software', 'resuelto', NULL, 5, 'Se arreglo cartucho atascado, se llego a la conclucion de que la impresora es la que no funciona.', NULL, NULL, '2025-07-01 16:36:26', '2025-07-01 17:37:38', '2025-07-02 00:14:47', NULL),
(48, '752', 'KARLA LETICIA MORALES GUEVARA', 'COMPARTIR CARPETA DE SGI EN MAQUINA DE AUX COMPRAS', 'la carpeta compartida SGI de aux de compras no permite el acceso', 'media', 'Accesos', 'abierto', NULL, NULL, NULL, NULL, NULL, '2025-07-03 14:46:15', NULL, NULL, NULL),
(49, '2726', 'Leonardo Villacorta', 'Actualización SolidWorks', 'Perdí acceso en la versión que estaba manejando, me avisas si necesitas algo de mi parte cuando vayas a realizar la reactivación.', 'media', 'Software', 'abierto', NULL, NULL, NULL, NULL, NULL, '2025-07-04 18:19:03', NULL, NULL, NULL),
(50, '2368', 'JENNIFER DANIELA TOLEDO NUÑEZ', 'Dificultad para acceder al sistema de materia prima & consumibles ', 'tengo dificultad para acceder a este sistema de materia prima y consumibles ', 'media', 'Accesos', 'resuelto', NULL, 5, 'se corroboro como funciona la conexion', NULL, NULL, '2025-07-07 15:10:50', '2025-07-07 22:29:34', '2025-07-07 23:00:32', NULL),
(51, '1192', 'ELIZABETH MURILLO GARCIA', 'NO PUEDO IMPRIMIR DESDE MICROSIP', 'No me permite imprimir desde MICROSIP...', 'alta', 'Impresoras', 'cerrado', NULL, 5, 'Se volvio a vincular impresora', 'satisfactoria', NULL, '2025-07-07 18:16:13', '2025-07-07 22:29:52', '2025-07-07 22:59:56', '2025-07-09 15:30:35'),
(52, '3185', 'Isaias Rios', 'Carpeta compartida sgi', 'No me deja entrar a la carpeta compartida aún así poniendo el usuario y la contraseña ', 'media', 'Accesos', 'resuelto', NULL, 4, 'Se le comento al compañero acerca del uso del sistema del sgi\n', NULL, NULL, '2025-07-07 21:40:16', '2025-07-07 22:30:49', '2025-07-07 22:31:09', NULL),
(53, '870', 'Pedro Hernandez Sarabia', 'TRABAJADOR NO REGISTRADO', '#3314 FRANCISCO SEGURA VAZQUEZ NO APARECE EN EL SISTEMA PARA HACER CAMBIO DE TURNO', 'media', 'Software', 'resuelto', NULL, 5, 'El trabajador sera registrado', NULL, NULL, '2025-07-08 14:07:17', '2025-07-10 17:06:40', '2025-07-10 17:06:54', NULL),
(54, '1192', 'ELIZABETH MURILLO GARCIA', 'NOMBRE DE IMPRESORA NO VALIDO', 'quiero imprimir y me aparece nombre de impresora no valido; cerré y volví a abrir todo y ahora no me aparece....', 'urgente', 'Impresoras', 'resuelto', NULL, 4, 'Se arreglo la incidencia, era error de microsip\n', NULL, NULL, '2025-07-09 15:30:20', '2025-07-15 14:30:10', '2025-07-15 14:30:31', NULL),
(55, '3174', 'Michell Martínez Rodríguez', 'Eliminar correo de la computadora', 'Hola! Cuándo estaba mi compañera anterior (Claudia), inició sesión con su correo en la computadora, hoy es fecha que no lo he podido quitar, me sale desde que inicio sesión, en todos los archvios..\r\nHe tratado de quitarlo pero todas las veces ha sido sin éxito, ayuda!!!!!!!!!!!!!', 'alta', 'Otro', 'cerrado', NULL, 5, 'Cuenta eliminada', 'satisfactoria', NULL, '2025-07-11 14:55:36', '2025-07-11 17:59:51', '2025-07-11 18:02:09', '2025-07-11 18:55:01'),
(56, '3142', 'ilse noemi reyes guajardo', 'LIMITE EXCEDIDO DE ENVIO DE CORREOS VIA NOMINA', 'SE EXCEDIO EL LIMITE DE CORREOS ENVIADOS ', 'alta', 'Email', 'resuelto', NULL, 4, 'Se soluciono comprando la licencia para Google Workspace', NULL, NULL, '2025-07-11 21:17:30', '2025-07-15 14:30:44', '2025-07-15 14:31:00', NULL),
(57, '2368', 'JENNIFER DANIELA TOLEDO NUEÑEZ', 'SISTEMA DE EVIDENCIAS FOTOGRAFICAS DE CALIDAD', 'en el sistema de evidencias fotográficas no me permite descargar la evidencia de la ot-3529, por daño en la descarga y están pendientes por resguardar esas evidencias ', 'media', 'Hardware', 'resuelto', NULL, 4, 'Se inicio el programa\n', NULL, NULL, '2025-07-11 22:50:52', '2025-07-15 14:31:03', '2025-07-15 14:31:13', NULL),
(58, '3185', 'Isaias Rios', 'Tablet de vigilancia (olvidó de contraseña)', 'Se le olvida la contraseña de su usuario a vigilante ', 'media', 'Accesos', 'cerrado', NULL, 5, 'tableta reseteada\n', 'satisfactoria', NULL, '2025-07-15 14:32:08', '2025-07-16 20:53:28', '2025-07-16 20:54:11', '2025-07-18 21:20:19'),
(59, '3185', 'Isaias Rios', 'Teléfono de vigilancia, no tiene linea', 'Teléfono no cuenta con línea ', 'media', 'Red', 'cerrado', NULL, 5, 'linea restaurada', 'satisfactoria', NULL, '2025-07-15 14:33:05', '2025-07-16 20:53:27', '2025-07-16 20:53:57', '2025-07-18 21:20:15'),
(60, '3185', 'Isaias Rios', 'Internet de vigilancia ', 'No hay internet en caseta de vigilancia ', 'media', 'Red', 'cerrado', NULL, 5, 'internet restaurado\n', 'satisfactoria', NULL, '2025-07-15 14:33:45', '2025-07-16 20:53:26', '2025-07-16 20:53:48', '2025-07-18 21:20:13'),
(61, '3185', 'Isaias Rios', 'Lap top ', 'Se bloqueó laptop ', 'media', 'Accesos', 'cerrado', NULL, 5, 'Laptop desbloqueada\n', 'satisfactoria', NULL, '2025-07-15 14:34:18', '2025-07-16 20:53:26', '2025-07-16 20:53:41', '2025-07-18 21:20:09'),
(62, '940', 'MARIO ALBERTO VELIZ GONZALEZ', 'FALLA EN SISTEMA DE VALES ELECTRONICOS', 'SE RECARGA MAL ', 'urgente', 'Hardware', 'resuelto', NULL, 5, 'Servicio Restaurado', NULL, NULL, '2025-07-17 21:26:21', '2025-07-17 21:29:44', '2025-07-17 21:29:53', NULL),
(63, '2368', 'JENNIFER DANIELA TOLEDO NUÑEZ', 'no puedo conectarme a internet desde mi otra computadora', 'una de las computadoras que tengo no logro conectarme a internet \r\nme dice red no identificada, apage el equipo, revise el cable y aun asi no puedo ', 'media', 'Hardware', 'resuelto', NULL, 4, 'revisaremos en cuanto la computadora se reinicie\n', NULL, NULL, '2025-07-18 14:57:22', '2025-07-18 17:22:09', '2025-07-18 17:22:22', NULL),
(64, '713', 'YESSIKA PALACIOS', 'SGI', 'No puedo accesar a la CARPETA compartida SGI, favor de darme acceso', 'alta', 'Accesos', 'resuelto', NULL, 4, 'se ajusto el usuario\n', NULL, NULL, '2025-07-18 15:55:09', '2025-07-18 17:22:23', '2025-07-18 17:22:42', NULL),
(65, '1962', 'ZURISADAY LOPEZ NAVARRO', 'REPARACION DE IMPRESORA', 'IMPRESORA DAÑADA DESDE HACE 5 MESES, SE PIENSA RECUPERAR PARA SER UTILIZADA PARA ELABORACION DE NOMINAS', 'baja', 'Impresoras', 'resuelto', NULL, 5, 'Se reparo la impresora, queda pendiente compra del rollo de la impresora \n', NULL, NULL, '2025-07-18 19:00:50', '2025-07-18 19:01:39', '2025-07-18 19:02:08', NULL),
(66, '1962', 'ZURISADAY LOPEZ NAVARRO', 'SISTEMA MATRIZ DE HABILIDADES ', 'REALIZAR SISTEMA PARA CARGAR MATRIZ DE HABILIDADES POR PUESTOS, PARA FILTRAR CAPACITACIONES A PROGRAMAR ', 'media', 'Otro', 'abierto', NULL, NULL, NULL, NULL, NULL, '2025-07-18 19:03:33', NULL, NULL, NULL),
(67, '1962', 'ZURISADAY LOPEZ NAVARRO', 'BUZON DE TRANSPARENCIA', 'SE DETECTA QUE PERSONAL EN GENERAL RENUNCIA POR INCONFORMIDADES EN LA EMPRESA, SIN EMBARGO NO SE ACERCAN AL DPTO DE RH A MANIFESTARLO HASTA RENUNCIAR\r\nESTE SISTEMA NOS AYUDA A FILTRAR INCONFORMIDADES ANONIMAS PARA SOLUCIONAR ANTES DE RENUNCIAS', 'media', 'Otro', 'resuelto', NULL, 5, 'Sistema desarrollado e implementado en la empresa\n', NULL, NULL, '2025-07-18 19:06:02', '2025-07-18 21:08:02', '2025-07-18 21:08:22', NULL),
(68, '3185', 'Isaias Rios', 'Vigilancia ', 'No hay internet en la caseta de vigilancia ', 'media', 'Red', 'resuelto', NULL, 4, 'Se reconecto el cable que estaba suelto\n', NULL, NULL, '2025-07-18 21:20:04', '2025-07-18 21:31:59', '2025-07-18 22:01:48', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('solicitante','it') NOT NULL DEFAULT 'solicitante',
  `departamento` varchar(50) DEFAULT NULL,
  `estado` enum('disponible','ocupado','ausente') DEFAULT 'disponible',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `ultimo_acceso` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `departamento`, `estado`, `fecha_creacion`, `ultimo_acceso`) VALUES
(4, 'Marcos Palomo', 'marcosp@famisa.mx', '$2y$10$DpBHLIVY3M6GYKm5wJ9bWuQIDN1.dOBQTz3uyWh2v9VNcIo3eUnwG', 'it', NULL, 'disponible', '2025-06-08 01:53:59', '2025-07-21 14:07:33'),
(5, 'Gilberto Treviño', 'gilbertot@famisa.mx', '$2y$10$QlkErPyC9T6vQNjbJLuS5Oy.dWpbe0HCJ1sLUBWRiV/BGza/OqHxC', 'it', NULL, 'disponible', '2025-06-08 01:53:59', '2025-07-21 14:25:13');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `actividades_it`
--
ALTER TABLE `actividades_it`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`),
  ADD KEY `asignado_a` (`asignado_a`),
  ADD KEY `dependencia_actividad_id` (`dependencia_actividad_id`);

--
-- Indices de la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indices de la tabla `comentarios_ticket`
--
ALTER TABLE `comentarios_ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `hitos_proyecto`
--
ALTER TABLE `hitos_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `proyecto_id` (`proyecto_id`);

--
-- Indices de la tabla `proyectos_it`
--
ALTER TABLE `proyectos_it`
  ADD PRIMARY KEY (`id`),
  ADD KEY `responsable_id` (`responsable_id`);

--
-- Indices de la tabla `recursos_actividades`
--
ALTER TABLE `recursos_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actividad_id` (`actividad_id`);

--
-- Indices de la tabla `reportes_gantt`
--
ALTER TABLE `reportes_gantt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generado_por` (`generado_por`);

--
-- Indices de la tabla `seguimiento_actividades`
--
ALTER TABLE `seguimiento_actividades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actividad_id` (`actividad_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tickets_estado` (`estado`),
  ADD KEY `idx_tickets_solicitante` (`solicitante_id`),
  ADD KEY `idx_tickets_asignado` (`asignado_a`),
  ADD KEY `idx_tickets_fecha` (`fecha_creacion`),
  ADD KEY `idx_numero_ficha` (`numero_ficha`),
  ADD KEY `idx_solicitante_nombre` (`solicitante_nombre`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_usuarios_rol` (`rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `actividades_it`
--
ALTER TABLE `actividades_it`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `comentarios_ticket`
--
ALTER TABLE `comentarios_ticket`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `hitos_proyecto`
--
ALTER TABLE `hitos_proyecto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyectos_it`
--
ALTER TABLE `proyectos_it`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `recursos_actividades`
--
ALTER TABLE `recursos_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportes_gantt`
--
ALTER TABLE `reportes_gantt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `seguimiento_actividades`
--
ALTER TABLE `seguimiento_actividades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `actividades_it`
--
ALTER TABLE `actividades_it`
  ADD CONSTRAINT `actividades_it_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos_it` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `actividades_it_ibfk_2` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `actividades_it_ibfk_3` FOREIGN KEY (`dependencia_actividad_id`) REFERENCES `actividades_it` (`id`);

--
-- Filtros para la tabla `archivos_adjuntos`
--
ALTER TABLE `archivos_adjuntos`
  ADD CONSTRAINT `archivos_adjuntos_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comentarios_ticket`
--
ALTER TABLE `comentarios_ticket`
  ADD CONSTRAINT `comentarios_ticket_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comentarios_ticket_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `hitos_proyecto`
--
ALTER TABLE `hitos_proyecto`
  ADD CONSTRAINT `hitos_proyecto_ibfk_1` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos_it` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyectos_it`
--
ALTER TABLE `proyectos_it`
  ADD CONSTRAINT `proyectos_it_ibfk_1` FOREIGN KEY (`responsable_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `recursos_actividades`
--
ALTER TABLE `recursos_actividades`
  ADD CONSTRAINT `recursos_actividades_ibfk_1` FOREIGN KEY (`actividad_id`) REFERENCES `actividades_it` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reportes_gantt`
--
ALTER TABLE `reportes_gantt`
  ADD CONSTRAINT `reportes_gantt_ibfk_1` FOREIGN KEY (`generado_por`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `seguimiento_actividades`
--
ALTER TABLE `seguimiento_actividades`
  ADD CONSTRAINT `seguimiento_actividades_ibfk_1` FOREIGN KEY (`actividad_id`) REFERENCES `actividades_it` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seguimiento_actividades_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
